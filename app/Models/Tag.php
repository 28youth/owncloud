<?php

namespace XigeCloud\Models;

use XigeCloud\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use ListScopes;
    
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['name', 'tag_category_id', 'description'];

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
