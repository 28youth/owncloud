<?php

namespace XigeCloud\Http\Controllers\API;

use Illuminate\Http\Request;
use XigeCloud\Models\Ability;
use XigeCloud\Http\Requests\AbilityRequest;

class AbilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = Ability::query()->get();

        return response()->json($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AbilityRequest $request, Ability $ability)
    {
        $ability->fill($request->all());
        $ability->save();

        return response()->json($ability, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Ability $ability
     * @return \Illuminate\Http\Response
     */
    public function show(Ability $ability)
    {
        return response()->json($ability);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Ability $ability
     * @return \Illuminate\Http\Response
     */
    public function update(AbilityRequest $request, Ability $ability)
    {
    	$ability->fill($request->all());
    	$ability->save();

    	return response()->json($ability, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Ability $ability
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ability $ability)
    {
    	$ability->delete();

    	return response()->json(null, 204);
    }
}
