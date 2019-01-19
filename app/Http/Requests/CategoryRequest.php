<?php

namespace XigeCloud\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => ['required', unique_validator('categories', false), 'max:20'],
            'symbol' => ['required', unique_validator('categories', false), 'max:10'],
            'config_number.*' => 'distinct|in:cateNo,YY,mm,dd,YY-mm-dd,YY-mm',
            'config_operate.*' => 'distinct|in:create,approval,publish,change,destroy',
            'config_ability.*' => 'distinct|in:upload,download,move,rename,preview,delete,isShare,isUpdate',
            'config_format.*' => 'distinct|in:online,text,image,audio,video,xls,othor',
            'config_path' => 'string|max:100',
            'description' => 'max:100',
            'is_lock' => 'numeric|in:0,1',
            'parent_id' => 'numeric',
        ];
        if (strtolower($this->getMethod()) === 'patch') {
            $rules = array_merge($rules, [
                'name' => [
                    'required',
                    'max: 20',
                    unique_validator('categories'),
                ],
                'symbol' => [
                    'required',
                    'max: 10',
                    unique_validator('categories'),
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
        return array_dot(trans('fields.category'));
    }
}
