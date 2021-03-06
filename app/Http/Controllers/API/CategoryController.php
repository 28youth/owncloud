<?php

namespace XigeCloud\Http\Controllers\API;

use Illuminate\Http\Request;
use XigeCloud\Models\Category;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use XigeCloud\Http\Requests\CategoryRequest;
use XigeCloud\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = Category::query()
            // ->with(['_parent', '_children'])
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();

        if ($request->has('page')) {

            return array_merge($list, [
                'data' => CategoryResource::collection($list['data'])
            ]);
        }
        return CategoryResource::collection($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request, Category $category)
    {
        $category->fill($request->all());
        return $category->getConnection()->transaction(function() use ($category) {
            $category->save();

            // 一级分类创建对应的表
            if (! $category->parent_id) {
                $this->makeFileTable($category->id);
            }

            return CategoryResource::make($category);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        // $category->load('roles');
        
        return CategoryResource::make($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->fill($request->all());
        $category->save();

        return CategoryResource::make($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        return $category->getConnection()->transaction(function () use ($category) {
            $category->delete();
            $category->roles()->detach();

            return response()->json(null, 204);
        });
    }

    protected function makeFileTable(int $id)
    {
        $table = 'files_'.$id;

        if (Schema::hasTable($table)) {
            return fasle;
            // Schema::dropIfExists($table);
        }
        Schema::create($table, function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('user_id')->comment('上传者');
            $table->char('hash', 32)->comment('文件hash值');
            $table->char('origin_name', 100)->comment('原文件名');
            $table->char('number', 50)->comment('文件编号');
            $table->char('filename', 100)->comment('文件名');
            $table->char('mime', 150)->comment('文件mime');
            $table->char('size', 50)->comment('文件大小');
            $table->decimal('width', 8, 2)->nullable()->comment('图片宽');
            $table->decimal('height', 8, 2)->nullable()->comment('图片高');
            $table->mediumInteger('category_id')->comment('所属分类');
            $table->mediumInteger('download_sum')->defaule(0)->comment('下载次数');
            $table->dateTime('expired_at')->nullable()->comment('文件过期时间');

            $table->timestamps();
            $table->unique('number');
        });
    }
}
