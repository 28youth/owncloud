<?php

namespace XigeCloud\Models;

use Illuminate\Support\Arr;
use XigeCloud\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Builder;

class File extends BaseModel
{
	use ListScopes;

	protected $primaryKey = 'number';

	protected $keyType = 'string';

	public $incrementing = false;

	protected $appends = ['uploader'];
	
	public function tags()
	{
		return $this->belongsToMany(Tag::class, 'file_has_tags', 'file_number', 'tag_id');
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
		$staff = app('oaServer')->getStaff([
			'filters' => "realname={$username};status_id>=0"
		]);

		$staffSn = !empty($staff) ? $staff[0]['staff_sn'] : '';

		return $query->where('user_id', $staffSn);
	}

	public function getUploaderAttribute()
	{
		$staff = app('oaServer')->getStaff($this->user_id);

		return !empty($staff) ? Arr::only($staff, ['staff_sn', 'realname']) : [];
	}
}
