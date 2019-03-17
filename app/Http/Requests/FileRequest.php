<?php

namespace XigeCloud\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
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
            'number' => 'required',
            'cate_id' => 'required|exists:categories,id',
            'origin_name' => 'required|max:100',
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
            'expired_at' => 'date_format:Y-m-d H:i:s'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return array_dot(trans('fields.file'));
    }
}
