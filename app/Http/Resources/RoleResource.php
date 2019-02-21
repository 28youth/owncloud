<?php

namespace XigeCloud\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'staff' => $this->getStaff($this->staff),
            'categories' => $this->format($this->categories),
        ];
    }

    protected function format($categories)
    {
        $cates = [];
        if ($categories->isNotEmpty()) {
            return $categories->map(function ($item) {
                return array_merge([
                    'name' => $item->name
                ], $item->pivot->toArray());
            });
        }
        return $cates;
    }

    protected function getStaff($staff): array
    {
        $list = [];
        if ($staff->isNotEmpty()) {
            $staff_sn = $staff->pluck('staff_sn');
            $staff = app('ssoService')->getStaff(['filters' => "staff_sn={$staff_sn};status_id>=0"]);
            $list = array_map(function ($v) {
                return [
                    'staff_sn' => $v['staff_sn'],
                    'realname' => $v['realname'],
                ];
            }, $staff);
        }

        return $list;
    }
}
