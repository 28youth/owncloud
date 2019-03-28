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
        'full_name',
        'parent_id',
        'policy_id',
        'max_size',
        'is_lock',
        'allow_expire',
        'allow_edit',
        'dirrule',
        'numberrule',
        'filetype',
    ];

    protected $casts = [
        'filetype' => 'array',
    ];

    public static function boot()
    {
        parent::boot();
        
        self::saving(function ($post) {
            $post->changeFullName();
        });
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_categories', 'category_id', 'role_id');
    }

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
        $symbol = $this->getOriginal('symbol');
        $original = $this->getOriginal('name');

        if (!empty($value) && $symbol != $value) {
            $this->attributes['symbol'] = strtoupper($value);

        } elseif (empty($value) || $original != $this->name) {

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

    public function parents()
    {
        return $this->_parent()->with('_parent');
    }

    public function getSymbolsAttribute()
    {
        if (!$parent = $this->_parent) {
            $symbols = '';
        } else {
            $symbols = $parent->symbols;
            $symbols .= $parent->symbol;
        }

        return $symbols;
    }

    private function changeFullName()
    {
        if ($this->isDirty('parent_id') || $this->isDirty('name')) {
            $newFullName = $this->parent_id > 0 ? $this->_parent->full_name . '-' . $this->name : $this->name;
            $this->full_name = $newFullName;
            $this->changeChildrenFullName($newFullName);
        } elseif ($this->isDirty('full_name')) {
            $newFullName = $this->full_name;
            $this->changeChildrenFullName($newFullName);
        }
    }
    
    private function changeChildrenFullName($fullName)
    {   
        if(!empty($this->_children)) {
            $this->_children->each(function ($item) use ($fullName) {
                $item->full_name = $fullName . '-' . $item->name;
                $item->save();
            });
        }
    }
}
