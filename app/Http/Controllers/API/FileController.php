<?php

namespace XigeCloud\Http\Controllers\API;

use Hashids;
use Storage; 
use XigeCloud\Files\Handler;
use Illuminate\Http\Request;
use XigeCloud\Files\FlysystemManager;
use XigeCloud\Http\Requests\FileUpload;
use XigeCloud\Http\Requests\ChunkUpload;
use XigeCloud\Http\Requests\UploadRequest;

class FileController extends Controller
{
    protected $cateID;

    protected $handler;

    public function __construct(Request $request)
    {
        $this->cateID = $request->cate_id;
        $this->handler = new Handler($this->cateID);
    }

    public function store(FileUpload $request)
    {
        $file = $request->file('file');
        $cateID = $request->input('cate_id');
        return (new Handler($cateID))->storage($request, $file);
    }

    // 上传前验证
    public function ckfile(UploadRequest $request)
    {
        return response()->json(['isUpload' => 'ok'], 200);
    }

    public function chunk(Request $request)
    {
        $chunk = $request->file('chunk');
        $chunkID = $request->input('chunk_id');
        $chunkSum = $request->input('chunk_sum');

        return $this->handler->setChunk($chunkID, $chunkSum, $chunk);
    }
}
