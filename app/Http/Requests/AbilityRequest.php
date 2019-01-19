<?php

namespace XigeCloud\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbilityRequest extends FormRequest
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
            'name' => ['required', unique_validator('abilities', false), 'max:50'],
            'parent_id' => 'numeric',
            'is_lock' => 'numeric|in:0,1',
            'sort' => 'numeric',
        ];
        if (strtolower($this->getMethod()) === 'patch') {
            $rules = array_merge($rules, [
                'name' => [
                    'required','max: 50',
                    unique_validator('abilities', true, true),
                ],
            ]);
        }

        return $rules;
    }
}
