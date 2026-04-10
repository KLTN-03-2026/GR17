<?php

namespace App\Http\Requests;

class AdminSearchRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'so_dien_thoai' => 'sometimes|string',
            'email' => 'sometimes|string',
            'name' => 'sometimes|string',
        ];
    }
}
