<?php

namespace XigeCloud\Http\Controllers\API;

use Illuminate\Http\Request;
use XigeCloud\Models\FileLog;
use XigeCloud\Http\Resources\FileLogResource;

class FileLogController extends Controller
{
	public function index(Request $request)
	{
		$list = FileLog::query()
			->filterByQueryString()
			->sortByQueryString()
			->withPagination();

		if ($request->has('page')) {

            return array_merge($list, [
                'data' => FileLogResource::collection($list['data'])
            ]);
        }

        return FileLogResource::collection($list);
	}
}
