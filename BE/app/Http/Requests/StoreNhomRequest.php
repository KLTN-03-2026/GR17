<?php

namespace App\Http\Requests;

class StoreNhomRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ten_nhom' => 'required|string|max:100',
        ];
    }
}
