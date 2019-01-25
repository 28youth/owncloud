<?php

namespace XigeCloud\Models;

use Overtrue\Pinyin\Pinyin;
use XigeCloud\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use ListScopes;
    
	protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
    	'name',
        'symbol',
    	'parent_id',
    	'is_lock',
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

    public function setParentIdAttribute($value)
    {
    	$this->attributes['parent_id'] = !empty($value) ? $value : 0;
    }

    public function setIsLockAttribute($value)
    {
        $this->attributes['is_lock'] = !empty($value) ? $value : 0;
    }
    
    public function setSymbolAttribute($value)
    {
        $original = $this->getOriginal('name');
        if ($original != $this->name) {
            $pinyin = new Pinyin();
            $this->attributes['symbol'] = strtoupper($pinyin->abbr($this->name));
        }
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
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
