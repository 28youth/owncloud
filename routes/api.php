<?php

use Illuminate\Http\Request;
use XigeCloud\Http\Controllers\APIs;
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

	// 获取分类列表
	$api->apiResource('file/categories', APIs\CategoryController::class);

	// 获取权限列表
	$api->apiResource('file/abilities', APIs\AbilityController::class);

	// 获取角色列表
	$api->apiResource('roles', APIs\RoleController::class);
});