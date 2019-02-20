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
        $category->symbol = $request->symbol;
        $category->is_lock = $request->is_lock;
        $category->parent_id = $request->parent_id;

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
        return response()->json($category);
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
        $category->symbol = $request->symbol;
        $category->is_lock = $request->is_lock;
        $category->parent_id = $request->parent_id;
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
        $category->delete();

        return response()->json(null, 204);
    }

    protected function makeFileTable(int $id)
    {
        $table = 'files_'.$id;

        if (Schema::hasTable($table)) {

            Schema::dropIfExists($table);
        }
        Schema::create($table, function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('user_id')->comment('上传者');
            $table->char('hash', 32)->comment('文件hash值');
            $table->char('origin_filename', 100)->comment('原文件名');
            $table->char('number', 50)->comment('文件编号');
            $table->char('filename', 100)->comment('文件名');
            $table->char('mime', 150)->comment('文件mime');
            $table->char('size', 50)->comment('文件大小');
            $table->mediumInteger('category_id')->comment('所属分类');

            $table->timestamps();
        });
    }
}
