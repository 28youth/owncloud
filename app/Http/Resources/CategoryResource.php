<?php

namespace XigeCloud\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'full_name' => $this->full_name,
            'symbol' => $this->symbol,
            'parent_id' => $this->parent_id,
            'policy_id' => $this->policy_id,
            'is_lock' => $this->is_lock,
            'allow_expire' => $this->allow_expire,
            'dirrule' => $this->dirrule,
            'numberrule' => $this->numberrule,
            'filetype' => $this->filetype,
            'max_size' => $this->max_size,
            'description' => $this->description,
        ];
    }
}
