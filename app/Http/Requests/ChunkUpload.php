<?php

namespace XigeCloud\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChunkUpload extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return (bool)$this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'chunk' => 'required|file',
            'chunk_id' => 'required|numeric|max:9999',
            'chunk_sum' => 'required|numeric|max:9999',
        ];
    }

    /**
     * Get the validation message that apply to the request.
     * 
     * @return array
     */
    public function messages(): array
    {
        return [
            'chunk.required' => '没有上传分片或者上传错误',
            'chunk.file' => '分片上传失败',
            'chunk_id.required' => '分片编号不能为空',
            'chunk_sum.required' => '分片总数不能为空',
        ];
    }
}
