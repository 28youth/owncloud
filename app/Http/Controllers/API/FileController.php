<?php

namespace XigeCloud\Http\Controllers\API;

use Hashids;
use Storage; 
use XigeCloud\Files\Handler;
use Illuminate\Http\Request;
use XigeCloud\Services\FileUpdate;
use XigeCloud\Models\File as FileModel;
use XigeCloud\Http\Requests\FileUpload;
use XigeCloud\Http\Requests\FileRequest;
use XigeCloud\Http\Requests\ChunkUpload;
use XigeCloud\Http\Requests\UploadRequest;
use XigeCloud\Http\Resources\FileResource;

class FileController extends Controller
{
    protected $handler;

    public function index(Request $request)
    {
        $uploader = $request->query('uploader');

        $file = FileModel::cate($this->resolveHandler()->getTableID())
            ->when(!empty($uploader), function ($query) use ($uploader) {
                return $query->byUser($uploader);
            })
            ->with(['category', 'tags'])
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();

        if ($request->has('page')) {

            return array_merge($file, [
                'data' => FileResource::collection($file['data'])
            ]);
        }

        return FileResource::collection($file);
    }
    
    /**
     * 单位文件上传.
     * 
     * @param  FileUpload $request
     * @return mixed
     */
    public function store(FileUpload $request)
    {
        $file = $request->file('file');

        return $this->resolveHandler()->storage($request, $file);
    }

    public function batchStore(Request $request)
    {
        $type = $request->input('uptype', 'file');
        if ($type === 'file') {
            
            return $this->resolveHandler()->setChunk($chunk);
        }
    }

    /**
     * 快速上传查询：
     *     1、查询文件是否上传
     *     2、文件合法性验证
     * @param  \XigeCloud\Http\Requests\UploadRequest $request
     * 
     * @return mixed
     */
    public function rapidUpload(UploadRequest $request)
    {
        $file = FileModel::cate($this->resolveHandler()->getTableID())
            ->where('md5', $request->md5)
            ->first();
        
        if ($file->isNotEmpty()) {

            return response()->json([
                'status' => 0,
                'info' => $file,
            ], 200);
        }
        return response()->json(['status' => 404]);
    }

    // 上传分片
    public function chunk(Request $request)
    {
        $chunk = $request->file('file');

        return $this->resolveHandler()->setChunk($chunk);
    }

    /**
     * 完成上传通知并生成文件.
     * 
     * @param  Request $request
     * @return mixed
     */
    public function mkfile(Request $request)
    {
        $type = $request->input('type');
        $filename = $request->input('name');
        $blockList = $request->input('block_list');
        if ($type === 'compress') {
            $filename = $request->input('name');
            $block = $request->input('block_list');

            return $this->resolveHandler()->createCompressFile($blockList, $filename);
        }

        return $this->resolveHandler()->createFile($blockList, $filename);
    }

    /**
     * 更新文件信息.
     * 
     * @param  FileRequest $request
     * @return mixed
     */
    public function update(FileRequest $request)
    {
        $tabID = $this->resolveHandler()->getTableID();
        $fileService = new FileUpdate($tabID, $request->number);
        $response = $fileService->update($request->all());

        return response()->json($response);
    }

    public function resolveHandler()
    {
        if (! ($this->handler instanceof Handler)) {
            $this->handler = new Handler();
        }

        return $this->handler->setInit(request()->cate_id);
    }
}
