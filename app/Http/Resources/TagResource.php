<?php

namespace XigeCloud\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
            'tag_category_id' => $this->tag_category_id,
            'description' => $this->description,
            'category' => $this->category,
        ];
    }
}
