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
            'staff' => 'array',
            'abilities' => 'array',
            'abilities.*' => 'exists:abilities,id',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ];
        if ($this->getMethod() === 'PATCH') {
            $rules = array_merge($rules, [
                'name' => [
                    'required',
                    unique_validator('roles'),
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
        return array_dot(trans('fields.role'));
    }
}
