<?php 

namespace XigeCloud\Files;

use Cache;
use Illuminate\Http\Request;
use XigeCloud\Models\Category;
use League\Flysystem\Filesystem;
use Illuminate\Http\UploadedFile;
use XigeCloud\Files\Plugin\MergeFile;
use XigeCloud\Files\FlysystemManager;
use XigeCloud\Models\File as FileModel;
use XigeCloud\Models\Chunk as ChunkModel;


class Handler
{   
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
        $fileModel = $this->validateFileInDb($file, function (UploadedFile $file, string $hash) {

            $filepath = makeFilePath($this->category->dirrule);
            $filename = makeFileName($this->category->namerule, $file->getClientOriginalName());
            $response = $this->filesystem()->put(($filepath.$filename), $file->get());
            if ($response === false) {
                return $response->json(['message' => '上传失败'], 500);
            }
            $fileModel = new FileModel();
            $fileModel->setTable('files_'.$this->tabID);
            $fileModel->hash = $hash;
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

    public function setChunk($chunkId, $chunkSum, $file)
    {
        return $this->saveChunk($chunkId, $chunkSum, function ($chunk) use ($file){

            $path = '/chunks/'.$chunk->chunk_name.'.chunk';
            $response = $this->filesystem()->put($path, $file->get());
            switch (true) {
                case request()->ok:
                    return $this->makeFile($chunk->file_hash, function ($tmpfile, $hash) use ($file) {
                        // 生成文件存储位置并保存
                        $filename = sprintf(
                            '%s%s',
                            makeFilePath($this->category->dirrule),
                            makeFileName($this->category->namerule, $file->getClientOriginalName())
                        );
                        $response = $this->filesystem()->copy($tmpfile, $filename);
                        if ($response === false) {
                            return $response->json(['message' => '上传失败'], 500);
                        }

                        $fileModel = new FileModel();
                        $fileModel->setTable('files_'.$this->tabID);
                        $fileModel->hash = $hash;
                        $fileModel->size = $file->getClientSize();
                        $fileModel->mime = $file->getClientMimeType();
                        $fileModel->user_id = request()->user()->staff_sn;
                        $fileModel->filename = $filename;
                        $fileModel->category_id = $this->category->id;
                        $fileModel->origin_name = $file->getClientOriginalName();
                        $fileModel->saveOrFail();

                        return $fileModel;
                    });
                    break;

                case $response !== true:
                    abort(500, '上传失败');
                    break;
                    
                default:
                    return 1;
                    break;
            }
        });

    }

    protected function saveChunk($chunkId, $chunkSum, callable $call)
    {
        $chunkModel = new ChunkModel();
        $chunkModel->up_time = now();
        $chunkModel->cate_id = $this->category->id;
        $chunkModel->user_id = request()->user()->staff_sn;
        $chunkModel->chunk_id = $chunkId;
        $chunkModel->chunk_sum = $chunkSum;
        $chunkModel->chunk_key = str_random();
        $chunkModel->chunk_name = str_random(8);
        $chunkModel->file_hash = request()->hash;
        $chunkModel->saveOrFail();

        return call_user_func_array($call, [$chunkModel]);
    }

    protected function makeFile(string $hash, callable $call)
    {
        $filesystem = $this->filesystem();
        $chunks = ChunkModel::where('file_hash', $hash)
            ->orderBy('chunk_id', 'asc')
            ->get();

        // 生成空文件
        $fileName = '/chunks/'.str_random(8);
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

    // 根据分类设置文件存储表（当前分类的第一级分类）
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
    
    // 文件路径生成
    protected function makeDefaultPath($fileKey = 0): string
    {
        $filePath = $fileKey ?: $this->getKey();
        $filePath = (strlen($filePath) < 11) ? str_pad($filePath, 11, '0', STR_PAD_LEFT) : $filePath;
        return sprintf(
            '%s/%s/%s/%s',
            substr($filePath, 0, 3),
            substr($filePath, 3, 3),
            substr($filePath, 6, 3),
            substr($filePath, 9)
        );
    }

    // 文件名生成
    protected function makeFileName(UploadedFile $file)
    {
        $originame = $file->getClientOriginalName();
        $filename = sprintf(
            '%s_%s_%s',
            $this->getKey(),
            $this->category->id,
            makeFileName($this->category->namerule, $originame)
        );

        return $filename;
    }

    // 文件编号生成
    protected function makeFileNumber()
    {
        $cates = Category::where('id', $this->category->id)
            ->select('parent_id','symbol')
            ->with('_parent')->first();

        return sprintf(
            '%s%s%s',
            $cates->symbols,
            $cates->symbol,
            $this->getKey()
        );
    }

    /**
     * 计算文件存储key.
     * 
     * @return int
     */
    protected function getKey(): int
    {
        $key = FileModel::cate($this->tabID)->orderBy('id', 'desc')->value('id');

        return empty($key) ? 1 : (int)$key + 1;
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
        $cacheKey = "policyName_{$this->category->id}";

        if (!Cache::has($cacheKey)) {
            $name = $this->category->policy['name'] ?? '';

            Cache::forever($cacheKey, $name);
        }
        return Cache::get($cacheKey);
    }
}