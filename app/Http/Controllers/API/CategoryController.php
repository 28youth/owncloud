<?php

namespace XigeCloud\Http\Controllers\API;

use Illuminate\Http\Request;
use XigeCloud\Models\Category;
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
        $category->save();

        return response()->json($category);
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
}
