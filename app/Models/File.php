<?php

namespace XigeCloud\Models;

use XigeCloud\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Builder;

class File extends BaseModel
{
	use ListScopes;

	
	public function tags()
	{
		return $this->belongsToMany(Tag::class, 'file_has_tags');
	}

	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	/**
	 * search file by username.
	 * 
	 * @param  \Illuminate\Database\Eloquent\Builder $query
	 * @param  string $username 
	 * 
	 * @return \Illuminate\Database\Eloquent\Builder;
	 */
	public function scopeByUser(Builder $query, string $username)
	{
		$staff = app('ssoService')->getStaff([
			'filters' => "realname={$username};status_id>=0"
		]);
		$staffSn = !empty($staff) ? $staff[0]['staff_sn'] : '';

		return $query->where('user_id', $staffSn);
	}
}
