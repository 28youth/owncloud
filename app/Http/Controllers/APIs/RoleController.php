<?php

namespace XigeCloud\Http\Controllers\APIs;

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
                $list = array_map(function ($k, $v) use ($role) {
                    return [
                        'staff_sn' => $v,
                        'role_id' => $role->id,
                    ];
                }, $data['staff']);

                \DB::table('staff_has_roles')->where('role_id', $role->id)->delete();
                \DB::table('staff_has_roles')->insert($list);
            }
            
            $role->load(['abilities', 'categories']);

            return response()->json($role, 201);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
