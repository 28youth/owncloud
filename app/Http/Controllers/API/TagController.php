<?php

namespace XigeCloud\Http\Controllers\API;

use Illuminate\Http\Request;
use XigeCloud\Models\Tag as TagModel;
use XigeCloud\Http\Requests\TagRequest;
use XigeCloud\Http\Resources\TagResource;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = TagModel::query()
            ->with('category')
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();

        if ($request->has('page')) {
            return array_merge($list, [
                'data' => TagResource::collection($list['data'])
            ]);
        }
        return TagResource::collection($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TagRequest $request, TagModel $tag)
    {
        $tag->fill($request->all());
        $tag->save();

        return response()->json(TagResource::make($tag), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TagModel $tag)
    {
        $tag->load('category');

        return TagResource::make($tag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TagRequest $request, TagModel $tag)
    {
        $tag->fill($request->all());
        $tag->save();

        return response()->json(TagResource::make($tag), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagModel $tag)
    {
        $tag->delete();

        return response()->json(null, 204);
    }
}
