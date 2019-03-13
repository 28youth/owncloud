<?php 

namespace XigeCloud\Files;

use Cache;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use XigeCloud\Models\Policy;
use XigeCloud\Models\Category;
use League\Flysystem\Filesystem;
use Illuminate\Http\UploadedFile;
use XigeCloud\Services\RedisLock;
use XigeCloud\Files\Plugin\MergeFile;
use XigeCloud\Files\FlysystemManager;
use XigeCloud\Models\File as FileModel;
use XigeCloud\Models\Chunk as ChunkModel;
use XigeCloud\Files\Traits\FileHandle;

class Handler
{   
    use FileHandle;

    protected $tabID;

    protected $connect;

    protected $category;

    protected $filesystem;

    public function __construct(int $cate_id)
    {
        $this->category = Category::find($cate_id);
        $this->connect = $this->cacheConnectName();
        $this->setTabId($cate_id);
        $this->setFilesystem();
    }

    /**
     * 单文件存储.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Http\UploadedFile $file
     * 
     * @return \XigeCloud\Models\File
     */
    public function storage(Request $request, UploadedFile $file)
    {
        $fileModel = $this->validateFileInDb($file, function (UploadedFile $file, string $hash) {

            $info = $this->makeFileInfo();
            $originame = $file->getClientOriginalName();
            $savepath = sprintf('%s%s%s', $info['path'], $info['number'], $originame);

            $response = $this->filesystem()->put($savepath, $file->get());
            if ($response === false) {
                return $response->json(['message' => '上传失败,请重试'], 500);
            }
            $fileModel = new FileModel();
            $fileModel->setTable('files_'.$this->tabID);
            $fileModel->hash = $hash;
            $fileModel->number = $info['number'];
            $fileModel->size = $file->getClientSize();
            $fileModel->mime = $file->getClientMimeType();
            $fileModel->user_id = request()->user()->staff_sn;
            $fileModel->filename = $savepath;
            $fileModel->category_id = $this->category->id;
            $fileModel->origin_name = $originame;
            $fileModel->saveOrFail();

            return $fileModel;
        });

        return response()->json($fileModel, 201);
    }

    protected function validateFileInDb(UploadedFile $file, callable $call): FileModel
    {
        $hash = md5_file($file->getRealPath());

        return FileModel::cate($this->tabID)
            ->where('hash', $hash)
            ->firstOr(function () use ($file, $call, $hash): FileModel {
                return call_user_func_array($call, [$file, $hash]);
        });
    }

    public function setChunk(UploadedFile $file)
    {
        $chunkmd5 = md5_file($file->getRealPath());

        $chunkpath = sprintf('/chunks/%s.chunk', $chunkmd5);

        $response = $this->filesystem()->put($chunkpath, $file->get());

        if ($response !== true) {
            abort(500, '上传失败，请重试！');
        }

        return response()->json([
            'md5' => request()->header('Content-MD5')
        ]);
    }

    /**
     * 合并分片并保存文件信息.
     * 
     * @param  array $block_list
     * @return mixed
     */
    public function createFile($block_list, $filename)
    {
        $chunkpath = $this->getPath('chunks');
        $savename = sprintf('%s%s', Str::random(), $filename);

        $merge = $this->mergeFile($chunkpath, $savename, $block_list);
        if (! empty($merge)) abort(500, $merge);

        $tmpfile = sprintf('/chunks/%s', $savename);

        return $this->moveInPath($tmpfile, $filename);
    }

    /**
     * 创建压缩包文件.
     * 
     * @param  array $block_list
     * @param  string $filename
     * 
     * @return mixed
     */
    public function createCompressFile($block_list, $filename)
    {
        $chunkpath = $this->getPath('chunks');
        $savename = sprintf('%s%s', Str::random(), $filename);

        // 合并分片
        $merge = $this->mergeFile($chunkpath, $savename, $block_list);
        if (! empty($merge)) abort(500, $merge);

        // 解压缩
        $files = $this->unzip($chunkpath, $savename);
        if (! empty($files)) {
            $name = explode('.', $savename);
            $basepath = sprintf('/chunks/%s', reset($name));

            // 单文件操作
            foreach ($files as $key => $file) {
                $tmpfile = sprintf('%s/%s', $basepath, $file);
                $this->moveInPath($tmpfile, $filename);
            }
        }
        return response()->json(['message' => '操作成功']);
    }

    /**
     * 将临时文件移动到存储位置.
     * 
     * @param  string $tmpfile
     * @param  string $filename
     * 
     * @return mixed
     */
    protected function moveInPath($tmpfile, $filename)
    {
        $filesystem = $this->filesystem();
        $info = $this->makeFileInfo();
        $savepath = sprintf(
            '%s%s%s',
            $info['path'],
            $info['number'],
            $filename
        );
        $response = $filesystem->copy($tmpfile, $savepath);
        if ($response === false) abort(500, '上传失败');

        return $this->saveInDb($savepath, $info['number'], $filename);
    }

    /**
     * 保存文件信息到数据库.
     * 
     * @param  string $path
     * @param  string $number
     * @param  string $filename
     * 
     * @return mixed
     */
    protected function saveInDb($path ,$number, $filename)
    {
        $filesystem = $this->filesystem();
        $size = $filesystem->getSize($path);
        $mime = $filesystem->getMimetype($path);
        $table = sprintf('files_%s', $this->tabID);

        $fileModel = new FileModel();
        $fileModel->setTable($table);
        $fileModel->size = $size;
        $fileModel->mime = $mime;
        $fileModel->number = $number;
        $fileModel->filename = $path;
        $fileModel->origin_name = $filename;
        $fileModel->hash = request()->filehash;
        $fileModel->category_id = $this->category->id;
        $fileModel->user_id = request()->user()->staff_sn;
        $fileModel->saveOrFail();

        return $fileModel;
    }

    /**
     * 根据分类设置文件存储表（查找顶级分类）
     * 
     * @param int $cur_cate_id 分类ID
     *
     * @return void
     */
    protected function setTabId(int $cur_cate_id)
    {
        if ($this->category->parent_id === 0) {
            $this->tabID = $cur_cate_id;
        } else {
            $minutes = \Carbon\Carbon::now()->addDay();
            $cates = Cache::remember('category_map_key_with_pid', $minutes, function () {
                return Category::select(['id', 'parent_id'])->get()->mapWithKeys(function ($item) {
                    return [$item->id => $item->parent_id];
                });
            });
            while ($cates[$cur_cate_id]) {
                $cur_cate_id = $cates[$cur_cate_id];
            }
            $this->tabID = $cur_cate_id;
        }
    }

    /**
     * 文件编号生成.
     * 
     * @return string
     */
    protected function makeFileInfo()
    {   
        // 获取文件分类编号
        $cates = Category::where('id', $this->category->id)
            ->select('parent_id','symbol')
            ->with('_parent')->first();

        $symbol = $cates->symbols.$cates->symbol;

        $number = makeFileName($this->category->numberrule, $symbol);

        return [
            'number' => sprintf('%s%s', $number, $this->getKey($number)),
            'path' => makeFileName($this->category->dirrule, $symbol),
        ];
    }

    /**
     * 计算文件存储key.
     * 
     * @return int
     */
    protected function getKey($key)
    {
        $staffSn = request()->user()->staff_sn;
        $isLock = RedisLock::tryGetLock('file_lock', $staffSn, 5000);
        if ($isLock === true) {
            $orderKey = Cache::remember($key, now()->addMonth(), function () use ($key) {
                $number = FileModel::cate($this->tabID)->where('number', 'like', "%{$key}%")
                    ->where('category_id', $this->category->id)
                    ->latest()
                    ->value('number');
                if (empty($number)) return 1;
                return str_replace($key, '', $number) + 1;
            });
            Cache::increment($key);
            RedisLock::releaseLock('file_lock', $staffSn);

            return str_pad($orderKey, 5, 0, STR_PAD_LEFT);
        } else {
            return response()->json(['message' => '上传文件排队中'], 408);
        }
    }

    /**
     * Get the filesystem.
     * 
     * @return \League\Flysystem\Filesystem
     */
    protected function filesystem(): Filesystem
    {
        return app(FlysystemManager::class)->connection($this->connect);
    }

    protected function setFilesystem()
    {
        $this->filesystem = $this->filesystem();
    }

    /**
     * 缓存操作分类配置的策略名称.
     * 
     * @return string
     */
    protected function cacheConnectName(): string
    {
        $cacheKey = 'policies_mapwithkeys';
        $policies = Cache::rememberForever($cacheKey, function() {
            return Policy::get()->mapWithKeys(function ($item) {
                return [$item->id => $item->name];
            });
        });

        if ($policies->has($this->category->policy_id)) {
            return $policies->get($this->category->policy_id); 
        }
        abort(500, '当前分类未配置上传服务器');
    }

    // 获取文件表ID
    public function getTableID()
    {
        return $this->tabID;
    }

    // 老版本合并分片（弃用）
    protected function makeFile(string $hash, callable $call)
    {
        $filesystem = $this->filesystem();
        $chunks = ChunkModel::where('file_hash', $hash)
            ->orderBy('chunk_id', 'asc')
            ->get();

        // 生成空文件
        $fileName = '/chunks/'.Str::random(8);
        $fileObj = $filesystem->put($fileName, null);

        // 组装文件块
        try {
            $chunks->map(function ($item) use ($fileObj, $fileName, $filesystem) {
                $chunkPath = '/chunks/'.$item->chunk_name.'.chunk';
                $chunkObj = $filesystem->read($chunkPath);
                if (!$chunkObj || !$fileObj) {
                    abort(500, '文件创建失败');
                }
                $filesystem->addPlugin(new MergeFile);
                $filesystem->append($fileName, $chunkObj);

                $item->delete();
                $filesystem->delete($chunkPath);
            });
        } catch (\Exception $e) {
            $chunks->map(function ($item) use ($filesystem) {
                $item->delete();
                $filesystem->delete($chunkPath);
            });

            abort(500, '文件创建失败，请重试！');
        }

        $hash = md5_file($filesystem->getAdapter()->applyPathPrefix($fileName));

        return call_user_func_array($call, [$fileName, $hash]);
    }
}