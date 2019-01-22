<?php

namespace XigeCloud\Http\Controllers\API;

use XigeCloud\Models\Role;
use Illuminate\Http\Request;
use XigeCloud\Http\Requests\RoleRequest;
use XigeCloud\Http\Resources\RoleResource;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = Role::with('abilities', 'categories', 'staff')->get();

        return RoleResource::collection($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request, Role $role)
    {
        $data = $request->all();
        $role->name = $data['name'];

        return $role->getConnection()->transaction(function () use ($role, $data) {
            $role->save();
            if (!empty($data['abilities'])) {
                $role->abilities()->attach($data['abilities']);
            }
            if (!empty($data['categories'])) {
                $role->categories()->attach($data['categories']);
            }
            if (!empty($data['staff'])) {
                $list = array_map(function ($v) use ($role) {
                    return [
                        'staff_sn' => $v,
                        'role_id' => $role->id,
                    ];
                }, $data['staff']);

                \DB::table('staff_has_roles')->insert($list);
            }
            
            $role->load(['abilities', 'categories', 'staff']);

            return response()->json(RoleResource::make($role), 201);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  Role $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $role->load(['abilities', 'categories', 'staff']);

        return RoleResource::make($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Role $role
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        $data = $request->all();
        $role->name = $data['name'];

        return $role->getConnection()->transaction(function () use ($role, $data) {
            $role->save();
            if (!empty($data['abilities'])) {
                $role->abilities()->sync($data['abilities']);
            }
            if (!empty($data['categories'])) {
                $role->categories()->sync($data['categories']);
            }
            if (!empty($data['staff'])) {
                $list = array_map(function ($v) use ($role) {
                    return [
                        'staff_sn' => $v,
                        'role_id' => $role->id,
                    ];
                }, $data['staff']);

                $role->staff()->delete();
                \DB::table('staff_has_roles')->insert($list);
            }

            $role->load(['abilities', 'categories', 'staff']);

            return response()->json(RoleResource::make($role), 201);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        return $role->getConnection()->transaction(function () use ($role) {
            $role->staff()->delete();
            $role->delete();

            return response()->json(null, 204);
        });
    }
}
