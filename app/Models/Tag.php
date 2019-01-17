<?php

namespace XigeCloud\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Has the category of tag.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
    	return $this->hasOne(TagCategory::class, 'id', 'tag_category_id');
    }
}
