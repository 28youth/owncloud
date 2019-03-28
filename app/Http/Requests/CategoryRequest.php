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
            'symbol' => ['nullable', unique_validator('categories', false), 'max:10'],
            'dirrule' => ['required', 'max:100', 'regex:/^[a-zA-Z](?=.*\/).*(\})$/'],
            'numberrule' => ['required', 'max:100', 'regex:/^[a-zA-Z](?=.*\{.*\}).*(\})$/'],
            'policy_id' => 'required|exists:policies,id',
            'filetype' => 'distinct|array',
            'max_size' => 'numeric',
            'is_lock' => 'numeric|in:0,1',
            'allow_expire' => 'numeric|in:0,1',
            'description' => 'string|max:100',
        ];
        if (strtolower($this->getMethod()) === 'patch') {
            $rules = array_merge($rules, [
                'name' => [
                    'required',
                    'max: 20',
                    unique_validator('categories'),
                ],
                'symbol' => [
                    'nullable',
                    'max: 10',
                    unique_validator('categories'),
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
        return array_dot(trans('fields.category'));
    }
}
