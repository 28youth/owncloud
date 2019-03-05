<?php

namespace XigeCloud\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'size' => $this->size,
            'user_id' => $this->user_id,
            'number' => $this->number,
            'origin_name' => $this->origin_name,
            'uploader' => $this->uploader,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'download_sum' => $this->download_sum,
            'tags' => $this->tags,
            'category' => $this->category->only(['id', 'name', 'full_name']),
        ];
        // return parent::toArray($request);
    }
}
