<?php

namespace XigeCloud\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
            'name' => 'required|unique:roles,name,NULL,name,deleted_at,NULL|max:20',
            'staff' => 'required|array',
            'categories' => 'required|array',
            'categories.*.category_id' => 'required_with:categories|exists:categories,id',
            'categories.*.file_upload' => 'required_with:categories|boolean',
            'categories.*.file_download' => 'required_with:categories|boolean',
            'categories.*.file_edit' => 'required_with:categories|boolean',
            'categories.*.file_delete' => 'required_with:categories|boolean',
            'categories.*.file_expired' => 'required_with:categories|boolean',
            'categories.*.file_edit_tag' => 'required_with:categories|boolean',
        ];
        if (strtolower($this->getMethod()) === 'patch') {
            $rules = array_merge($rules, [
                'name' => [
                    'required',
                    unique_validator('roles'),
                ],
            ]);
        }

        return $rules;
    }

    /*public function testData()
    {
        [
            'name' => '管理员',
            'staff' => [
                '121833',
                '110105',
            ],
            'categories' => [
                [
                    'category_id' => 1,
                    'file_upload' => 1,
                    'file_download' => 1,
                    'file_edit' => 1,
                    'file_delete' => 1,
                    'file_expired' => 1,
                    'file_edit_tag' => 1,
                ],
                [
                    'category_id' => 2,
                    'file_upload' => 1,
                    'file_download' => 1,
                    'file_edit' => 1,
                    'file_delete' => 1,
                    'file_expired' => 1,
                    'file_edit_tag' => 1,
                ]
            ]
        ];
    }*/

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return array_dot(trans('fields.role'));
    }
}
