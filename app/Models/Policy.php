<?php

namespace XigeCloud\Models;

use XigeCloud\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use ListScopes;

    public $timestamps = false;

    protected $fillable = [
    	'name',
    	'driver',
    	'host',
    	'port',
    	'username',
    	'root',
    	'privatekey',
    	'timeout',
    ];

    public function categories()
    {
    	return $this->hasMany(Category::class, 'policy_id');
    }
}
