<?php

namespace XigeCloud\Models;

use Illuminate\Database\Eloquent\Model;

class TagCategory extends Model
{
	protected $hidden = ['created_at', 'updated_at'];
	
    /**
     * Has tags of the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags()
    {
        return $this->hasMany(Tag::class, 'tag_category_id', 'id')->orderBy('rank', 'desc');
    }
}
