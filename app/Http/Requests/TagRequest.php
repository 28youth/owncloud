<?php

namespace XigeCloud\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
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
            'name' => 'required|max:20|unique:tags',
            'tag_category_id' => 'required|exists:tag_categories,id',
            'description' => 'string|max:50',
        ];
        if (strtolower($this->getMethod()) === 'patch') {
            $rules = array_merge($rules, [
                'name' => [
                    'required',
                    unique_validator('tags')
                ]
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
        return array_dot(trans('fields.tags'));
    }
}
