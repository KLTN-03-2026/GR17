<?php

namespace App\Http\Requests;

class AdminChangeStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'is_block' => 'required|boolean',
        ];
    }
}
