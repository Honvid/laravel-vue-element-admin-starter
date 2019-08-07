<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class RoleRequest extends Request
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
                    'name'      => 'required|string|unique:roles|max:100',
                    'abilities' => 'array',
                    'title'     => 'string|max:200'
                ];
            case "PUT" :
                return [
                    'name'      => [
                        'required',
                        'string',
                        'max:100',
                        Rule::unique('roles')->ignore($this->get('id')),
                    ],
                    'abilities' => 'array',
                    'title'     => 'string|max:200'
                ];
        }
    }
}
