<?php

namespace XigeCloud\Models;

use XigeCloud\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    use ListScopes;

    protected $hidden = ['created_at', 'updated_at'];
    
    protected $fillable = [
        'name',
        'parent_id',
        'is_lock',
        'sort',
    ];
    
    public function setParentIdAttribute($value)
    {
        $this->attributes['parent_id'] = !empty($value) ? $value : 0;
    }

    public function setIsLockAttribute($value)
    {
        $this->attributes['is_lock'] = !empty($value) ? $value : 0;
    }

    public function _parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function _children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
