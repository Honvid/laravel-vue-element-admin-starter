<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UserRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'name'     => 'required|string|unique:users',
                    'email'    => 'email|unique:users',
                    'password' => 'required|string',
                ];
            case "PUT" :
                return [
                    'name'     => [
                        'string',
                        Rule::unique('users')->ignore($this->get('id')),
                    ],
                    'email'    => [
                        'email',
                        Rule::unique('users')->ignore($this->get('id')),
                    ],
                    'password' => 'string',
                ];
        }
    }
}
