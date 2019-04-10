<?php

namespace XigeCloud\Models;

use Illuminate\Support\Arr;
use XigeCloud\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Builder;

class File extends BaseModel
{
    use ListScopes;

    protected $primaryKey = 'number';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $appends = ['uploader'];

    protected $fillable = ['origin_name', 'expired_at'];
    
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'file_has_tags', 'file_number', 'tag_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 上传者ID搜索
    public function scopeByUser(Builder $query, int $user_id)
    {
        $staff = app('oaServer')->getStaff($user_id);
        $staffSn = !empty($staff) ? $staff['staff_sn'] : '';

        return $query->where('user_id', $staffSn);
    }

    public function getUploaderAttribute()
    {
        $staff = app('oaServer')->getStaff($this->user_id);

        return !empty($staff) ? Arr::only($staff, ['staff_sn', 'realname']) : [];
    }
    
    // 当前文件操作权限
    public function getAbilitiesAttribute()
    {
        return request()->user()->ability($this->category_id);
    }
}
