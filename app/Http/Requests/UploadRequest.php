<?php

namespace XigeCloud\Http\Requests;

use XigeCloud\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
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
            'cate_id' => 'required|exists:categories,id',
            'fileSize' => 'required|max:'.$max,
        ];
        if (!empty($mimes)) {
            $rules = array_merge($rules, [
                'fileExt' => [
                    'required',
                    Rule::in($mimes),
                ],
            ]);
        }
        return $rules;
    }

    /**
     * Get the validation message that apply to the request.
     * 
     * @return array
     */
    public function messages(): array
    {
        return [
            'cate_id.required' => '文件分类不能为空',
            'cate_id.exists' => '文件分类不存在',
            'fileSize.required' => '文件大小不能为空',
            'fileSize.max' => '文件大小超出限制',
            'fileExt.required' => '文件扩展不能为空',
            'fileExt.in' => '文件类型不允许上传',
        ];
    }
}
