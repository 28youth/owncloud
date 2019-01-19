<?php

namespace XigeCloud\Models;

use XigeCloud\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use ListScopes;
    
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['name'];

    public function abilities()
    {
        return $this->belongsToMany(Ability::class, 'role_has_abilities', 'role_id', 'ability_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'role_has_categories', 'role_id', 'category_id');
    }

    public function staff()
    {
        return $this->hasMany(StaffHasRole::class, 'role_id');
    }

    public function ability(string $ability)
    {
        return $this->abilities->keyBy('name')->get($ability, false);
    }
}
