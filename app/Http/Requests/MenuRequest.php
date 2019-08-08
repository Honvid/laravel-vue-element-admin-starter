<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class MenuRequest extends Request
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
                    'parent_id'   => 'required|numeric',
                    'title'       => 'required|string',
                    'icon'        => 'string',
                    'uri'         => 'required|unique:menus|string',
                    'permissions' => 'string',
                    'priority'    => 'numeric',
                    'link'        => 'boolean',
                    'show'        => 'boolean'
                ];
            case "PUT" :
                return [
                    'parent_id'   => 'required|numeric',
                    'title'       => 'required|string',
                    'icon'        => 'nullable|string',
                    'uri'         => [
                        'required',
                        'string',
                        Rule::unique('menus')->ignore($this->get('id')),
                    ],
                    'permissions' => 'nullable|string',
                    'priority'    => 'numeric',
                    'link'        => 'boolean',
                    'show'        => 'boolean'
                ];
        }
    }
}
