<?php

namespace XigeCloud\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
    	'name',
    	'parent_id',
    	'is_system',
    	'config_number',
    	'config_operate',
    	'config_ability',
    	'config_format',
    	'config_path',
    	'description'
    ];

    /**
     * Set the categories parent id.
     * 
     * @param string $value
     * @return void
     */
    public function setParentIdAttribute($value)
    {
    	$this->attributes['parent_id'] = !empty($value) ? $value : 0;
    }
}
