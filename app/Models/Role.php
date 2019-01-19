<?php

namespace XigeCloud\Models;

use XigeCloud\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use ListScopes;
    
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get all abilities of the role.
     * 
     * @return \Illuminate\Database\Eloquent\Concerns\belongsToMany
     */
    public function abilities()
    {
        return $this->belongsToMany(Ability::class, 'role_has_abilities', 'role_id', 'ability_id');
    }

    /**
     * Get or check The role ability.
     * 
     * @param  string $ability
     * @return \XigeCloud\Models\Ability
     */
    public function ability(string $ability)
    {
        return $this->abilities->keyBy('name')->get($ability, false);
    }
}
