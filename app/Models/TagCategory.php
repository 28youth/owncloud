<?php

namespace XigeCloud\Models;

use XigeCloud\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class TagCategory extends Model
{
    use ListScopes;
    
	protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'name',
        'color',
        'rank',
    ];
	
    /**
     * Has tags of the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags()
    {
        return $this->hasMany(Tag::class, 'tag_category_id', 'id');
    }
}
