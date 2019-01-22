<?php

namespace XigeCloud\Http\Controllers\API;

use Illuminate\Http\Request;
use GrahamCampbell\Flysystem\Facades\Flysystem;

class FileController extends Controller
{
	
	public function store(Request $request)
	{
		$res = Flysystem::put($request->file('file'), 'foo');

		dd($res);
	}
}
