<?php

namespace XigeCloud\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $cate_id = NULL;

    /**
     * 自定义操作表.
     * 
     * @param  int $cate_id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function cate($cate_id)
    {
        $instance = new static;
        $instance->setCate($cate_id);

        return $instance->newQuery();
    }

    /**
     * 根据分类ID设置操作表.
     * 
     * @param int $cate_id
     */
    public function setCate($cate_id)
    {
        $this->cate_id = $cate_id;
        if ($cate_id != NULL) {
            $this->table = $this->getTable().'_'.$cate_id;
        }
    }

    public function newInstance($attributes = [], $exists = false)
    {
        $model = parent::newInstance($attributes, $exists);

        empty($this->cate_id) && $model->setCate($this->cate_id);

        return $model;
    }
}