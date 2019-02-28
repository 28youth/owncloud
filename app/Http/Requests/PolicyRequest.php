<?php

namespace XigeCloud\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PolicyRequest extends FormRequest
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
            'name' => 'required|unique:policies|string|max:20',
            'driver' => 'required|string|max:6',
            'root' => 'required_if:driver,sftp',
            'host' => 'required_if:driver,sftp|ipv4',
            'port' => 'required_if:driver,sftp|numeric',
            'username' => 'required_if:driver,sftp|max:20',
            'privatekey' => 'required_if:driver,sftp',
            'timeout' => 'numeric',
        ];
        if (strtolower($this->getMethod()) === 'patch') {
            $rules = array_merge($rules, [
                'name' => [
                    'required',
                    unique_validator('policies')
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
        return array_dot(trans('fields.policies'));
    }
}
