<?php

namespace XigeCloud\Models;

use Illuminate\Database\Eloquent\Model;

class FileType extends Model
{

	public function _parent()
	{
		return $this->belongsTo(self::class, 'parent_id');
	}
	
	public function _children()
	{
		return $this->hasMany(self::class, 'parent_id');
	}
}
