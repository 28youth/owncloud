<?php 

namespace XigeCloud\Files;

use Cache;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use XigeCloud\Models\Category;
use League\Flysystem\Filesystem;
use Illuminate\Http\UploadedFile;
use XigeCloud\Services\RedisLock;
use XigeCloud\Files\Plugin\MergeFile;
use XigeCloud\Files\FlysystemManager;
use XigeCloud\Models\File as FileModel;
use XigeCloud\Models\Chunk as ChunkModel;
use XigeCloud\Files\Traits\BashHandle;

class Handler
{   
    use BashHandle;

    protected $tabID;

    protected $connect;

    protected $category;

    public function __construct(int $cate_id)
    {
        $this->category = Category::find($cate_id);
        $this->connect = $this->cacheConnectName();
        $this->setTabId($cate_id);
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
        return $this->getKey();
        $fileModel = $this->validateFileInDb($file, function (UploadedFile $file, string $hash) {

            $filepath = makeFilePath($this->category->dirrule);
            $filename = makeFileName($this->category->numberrule, $file->getClientOriginalName());
            $response = $this->filesystem()->put(($filepath.$filename), $file->get());
            if ($response === false) {
                return $response->json(['message' => '上传失败'], 500);
            }
            $fileModel = new FileModel();
            $fileModel->setTable('files_'.$this->tabID);
            $fileModel->hash = $hash;
            $fileModel->number = $this->makeFileNumber();
            $fileModel->size = $file->getClientSize();
            $fileModel->mime = $file->getClientMimeType();
            $fileModel->user_id = request()->user()->staff_sn;
            $fileModel->filename = ($filepath.$filename);
            $fileModel->category_id = $this->category->id;
            $fileModel->origin_name = $file->getClientOriginalName();
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
     * 检查分片是否上传(返回未上传的分片索引).
     * 
     * @param  array  $block_list
     * @return array
     */
    public function checkChunks(array $block_list)
    {
        $uploaded = [];
        $filesystem = $this->filesystem();
        collect($block_list)->map(function ($md5key, $key) use (&$uploaded, $filesystem)
        {
            $chunkpath = sprintf('/chunks/%s.chunk', $md5key);
            if (! $filesystem->has($chunkpath)) {

                array_push($uploaded, $key);
            }
        });

        // 全部分片都存在 ？impossible
        if (empty($uploaded)) {
            # code...
        }
        
        return $uploaded;
    }

    /**
     * 完成上传组织分片生成文件.
     * 
     * @param  array $block_list
     * @return mixed
     */
    public function createFile($block_list)
    {
        $savename = Str::random();
        $filesystem = $this->filesystem();
        $driver = $this->getConfig()['driver'];
        if ($driver === 'local') {
            $savepath = storage_path('chunks');
        } else {
            $savepath = $filesystem->getAdapter()->getRoot().'chunks';
        }
        return $this->execMerge($savepath, $savename, $block_list, function ($cb, $filename) use (
            $filesystem
        ) {
            if (! empty($cb)) abort(500, $cb);

            // 移动临时文件到存储位置
            $info = $this->makeFileInfo();
            $originame = request()->filename;
            $tmpfile = sprintf('/chunks/%s', $filename);
            $savepath = sprintf('%s%s%s', $info['path'], $info['number'], $originame);
            $response = $filesystem->copy($tmpfile, $savepath);
            if ($response === false) {
                return $response->json(['message' => '上传失败'], 500);
            }
            
            $fileModel = new FileModel();
            $fileModel->setTable('files_'.$this->tabID);
            $fileModel->hash = request()->filehash;
            $fileModel->number = $info['number'];
            $fileModel->size = $filesystem->getSize($savepath);
            $fileModel->mime = $filesystem->getMimetype($savepath);
            $fileModel->user_id = request()->user()->staff_sn;
            $fileModel->filename = $savepath;
            $fileModel->category_id = $this->category->id;
            $fileModel->origin_name = $originame;
            $fileModel->saveOrFail();

            return $fileModel;
        });
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
            'number' => sprintf('%s%s', $number, $this->getKey()),
            'path' => makeFileName($this->category->dirrule, $symbol),
        ];
    }

    /**
     * 计算文件存储key.
     * 
     * @return int
     */
    protected function getKey()
    {
        $staffSn = request()->user()->staff_sn;
        $isLock = RedisLock::tryGetLock('file_lock', $staffSn, 5000);
        if ($isLock === true) {
            $key = FileModel::cate($this->tabID)->orderBy('id', 'desc')->value('id');

            return empty($key) ? 1 : (int)$key + 1;

        } else {

            return response()->json(['message' => '上传文件排队中'], 403);
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

    /**
     * 缓存操作分类配置的策略名称.
     * 
     * @return string
     */
    protected function cacheConnectName(): string
    {
        $cacheKey = sprintf('policyName_%s', $this->category->id);

        if (! Cache::has($cacheKey)) {
            $name = $this->category->policy['name'] ?? '';
            Cache::forever($cacheKey, $name);
        }

        return Cache::get($cacheKey);
    }

    /**
     * 获取当前分类操作表ID
     */
    public function getTableID()
    {
        return $this->tabID;
    }
}