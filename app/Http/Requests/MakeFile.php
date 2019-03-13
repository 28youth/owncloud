<?php

namespace XigeCloud\Http\Requests;

use XigeCloud\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MakeFile extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!$this->cate_id) {

            return false;
        }

        return (bool)$this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $cate = Category::find($this->cate_id);
        $max = $cate->max_size ?? 0;
        $mimes = $cate->filetype ?? [];

        $rules = [
            'tags.*' => 'array|exists:tags,id', 
            'cate_id' => 'required|exists:categories,id',
            'block_list' => 'required|array',
            'filename' => 'required|max:100',
            'filesize' => 'required|max:'.$max,
        ];
        if (!empty($mimes)) {
            $rules = array_merge($rules, [
                'extension' => [
                    'required',
                    Rule::in($mimes),
                ],
            ]);
        }
        return $rules;
    }


    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return array_dot(trans('fields.mkfile'));
    }
}
