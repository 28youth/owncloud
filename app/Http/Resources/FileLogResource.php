<?php

namespace XigeCloud\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileLogResource extends JsonResource
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
            'changes' => $this->changes,
            'file_number' => $this->file_number,
            'operater' => $this->operater,
            'created_at' => $this->created_at->toDateString(),
        ];
    }
}
