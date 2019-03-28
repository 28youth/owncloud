<?php

namespace XigeCloud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class StaffHasRole extends Model
{
    public function scopeByStaffSn(Builder $query, $staff_sn)
    {
        return $query->where('staff_sn', $staff_sn);
    }
}
