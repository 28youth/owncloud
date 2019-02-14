<?php

namespace XigeCloud\Http\Requests;

use XigeCloud\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class FileUpload extends FormRequest
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
            'file' => 'required|max:'.$max.'|file',
        ];
        if (!empty($mimes)) {
            $rules = array_merge($rules, [
                'file' => 'required|max:'.$max.'|file|mimes:'.implode(',', $mimes),
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
            'file.required' => '没有上传文件或者上传错误',
            'file.max' => '文件上传超出服务器限制',
            'file.file' => '文件上传失败',
            'file.mimes' => '文件上传格式错误',
        ];
    }
}
