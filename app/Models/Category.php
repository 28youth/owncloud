<?php

namespace XigeCloud\Models;

use Overtrue\Pinyin\Pinyin;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
    	'name',
        'symbol',
    	'parent_id',
    	'is_system',
    	'config_number',
    	'config_operate',
    	'config_ability',
    	'config_format',
    	'config_path',
    	'description'
    ];

    protected $casts = [
        'config_number' => 'array',
        'config_operate' => 'array',
        'config_ability' => 'array',
        'config_format' => 'array',
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

    public function setSymbolAttribute($value)
    {
        $original = $this->getOriginal('name');
        if ($original != $this->name) {
            $pinyin = new Pinyin();
            $this->attributes['symbol'] = $pinyin->abbr($this->name);
        }
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
