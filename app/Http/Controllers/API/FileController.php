<?php

namespace XigeCloud\Http\Controllers\API;

use Hashids;
use Storage; 
use XigeCloud\Files\Handler;
use Illuminate\Http\Request;
use XigeCloud\Models\File as FileModel;
use XigeCloud\Http\Requests\FileUpload;
use XigeCloud\Http\Requests\ChunkUpload;
use XigeCloud\Http\Requests\UploadRequest;
use XigeCloud\Http\Resources\FileResource;

class FileController extends Controller
{
    protected $cateID;

    protected $handler;

    public function __construct(Request $request)
    {
        $this->cateID = $request->cate_id;
        $this->handler = new Handler($this->cateID);
    }

    public function index(Request $request)
    {
        $publisher = $request->query('publisher');

        $file = FileModel::cate($this->handler->getTable())
            ->when(!empty($publisher), function ($query) use ($publisher) {
                return $query->byUser($publisher);
            })
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
