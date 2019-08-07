<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AbilityRequest extends FormRequest
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
                    'name'  => 'required|string|unique:abilities|max:100',
                    'title' => 'string|max:200',
                    'group' => 'required|string|max:20',
                ];
            case "PUT" :
                return [
                    'name'      => [
                        'required',
                        'string',
                        'max:100',
                        Rule::unique('abilities')->ignore($this->get('id')),
                    ],
                    'title' => 'string|max:200',
                    'group' => 'required|string|max:20',
                ];
        }
    }
}
