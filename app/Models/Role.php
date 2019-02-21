<?php

namespace XigeCloud\Models;

use XigeCloud\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use ListScopes;
    
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['name'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'role_has_categories', 'role_id', 'category_id')
            ->withPivot([
                'file_edit',
                'file_delete',
                'file_upload',
                'file_expired',
                'file_download',
                'file_edit_tag'
            ]);
    }

    public function staff()
    {
        return $this->hasMany(StaffHasRole::class, 'role_id');
    }
}
