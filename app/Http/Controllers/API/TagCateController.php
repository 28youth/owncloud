<?php

namespace XigeCloud\Http\Controllers\API;

use Illuminate\Http\Request;
use XigeCloud\Models\TagCategory;
use XigeCloud\Http\Requests\TagCateRequest;

class TagCateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = TagCategory::withCount('tags')
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();

        return response()->json($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TagCateRequest $request, TagCategory $category)
    {
        $category->fill($request->all());
        $category->save();

        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  TagCategory $category
     * @return \Illuminate\Http\Response
     */
    public function show(TagCategory $category)
    {
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  TagCategory $category
     * @return \Illuminate\Http\Response
     */
    public function update(TagCateRequest $request, TagCategory $category)
    {
        $category->fill($request->all());
        $category->save();

        return response()->json($category, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TagCategory $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagCategory $category)
    {
        if (!$category->tags()->count()) {
            $category->delete();

            return response()->json(null, 204);
        }

        return response()->json(['message' => '该分类下还有标签存在，请先删除标签再删除分类'], 422);
    }
}
