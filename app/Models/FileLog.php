<?php

namespace XigeCloud\Models;

use Illuminate\Support\Arr;
use XigeCloud\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class FileLog extends Model
{
	use ListScopes;

	protected $fillable = [
        'user_id',
        'file_number',
        'changes',
        'operate_at',
        'operate_type',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    protected $appends = ['operater'];

    public function getOperaterAttribute()
    {
        $staff = app('oaServer')->getStaff($this->user_id);

        return !empty($staff) ? Arr::only($staff, ['staff_sn', 'realname']) : [];
    }

    public function getChangesAttribute($value)
    {
    	return $value;
    }
}
