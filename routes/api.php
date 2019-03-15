<?php

use Illuminate\Http\Request;
use XigeCloud\Http\Controllers\API;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Routing\Registrar as RouteContract;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function (RouteContract $api) {

	// 获取文件日志列表
	$api->get('file/logs', API\FileLogController::class.'@index');
	
	// 文件分块上传前查询
	$api->get('file/chunk', API\FileController::class.'@rapidUpload');

	// 文件分块上传
	$api->post('file/chunk', API\FileController::class.'@chunk');

	// 生成文件
	$api->post('file/mkfile', API\FileController::class.'@mkfile');

	// 文件操作
	$api->apiResource('files', API\FileController::class);

	// 文件分类
	$api->apiResource('categories', API\CategoryController::class);

	// 文件权限
	$api->apiResource('abilities', API\AbilityController::class);

	// 角色管理
	$api->apiResource('roles', API\RoleController::class);

	// 服务器配置
	$api->apiResource('policies', API\PolicyController::class);

	// 标签管理
	$api->apiResource('tags', API\TagController::class);
	
	// 获取标签分类列表
	$api->apiResource('tags/categories', API\TagCateController::class);

});