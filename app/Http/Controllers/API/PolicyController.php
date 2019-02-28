<?php

namespace XigeCloud\Http\Controllers\API;

use Illuminate\Http\Request;
use XigeCloud\Http\Requests\PolicyRequest;
use XigeCloud\Models\Policy as PolicyModel;
use XigeCloud\Http\Resources\PolicyResource;
class PolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = PolicyModel::query()
            ->with('categories')
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();

        if ($request->has('page')) {

            return array_merge($list, [
                'data' => PolicyResource::collection($list['data'])
            ]);
        }
        return PolicyResource::collection($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PolicyRequest $request, PolicyModel $policy)
    {
        $policy->fill($request->all());
        $policy->save();

        return response()->json(PolicyResource::make($policy), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PolicyModel $policy)
    {
        $policy->load('categories');

        return PolicyResource::make($policy);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PolicyRequest $request, PolicyModel $policy)
    {
        $policy->fill($request->all());
        $policy->save();

        return response()->json(PolicyResource::make($policy), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PolicyModel $policy)
    {
        $policy->delete();
        
        return response()->json(null, 204);
    }
}
