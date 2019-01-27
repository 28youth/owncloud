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
    	'operate',
    	'abilities',
    	'dirrule',
    	'namerule',
    	'filetype',
    	'description'
    ];

    protected $casts = [
        'operate' => 'array',
        'ability' => 'array',
        'dirrule' => 'array',
        'namerule' => 'array',
        'filetype' => 'array',
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

    public function policy()
    {
        return $this->belongsTo(Policy::class, 'policy_id');
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
