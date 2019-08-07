<?php

namespace App\Http\Requests;


class PaginateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page'     => 'int',
            'pageSize' => 'int',
            'name'     => 'string'
        ];
    }
}
