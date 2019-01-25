<?php

namespace XigeCloud\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	/**
	 * 文件分类ID
	 * 
	 * @var null
	 */
	protected $category = NULL;

	/**
	 * 自定义操作表.
	 * 
	 * @param  int $category
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public static function category($category)
	{
	    $instance = new static;
	    $instance->setCategory($category);

	    return $instance->newQuery();
	}

	/**
	 * 根据分类ID设置操作表.
	 * 
	 * @param int $category
	 */
	public function setCategory($category)
	{
	    $this->category = $category;
	    if ($category != NULL) {
	        $this->table = $this->getTable().'_'.$category;
	    }
	}

	public function newInstance($attributes = [], $exists = false)
	{
	    $model = parent::newInstance($attributes, $exists);

	    $model->setCategory($this->category);

	    return $model;
	}
}