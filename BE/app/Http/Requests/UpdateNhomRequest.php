<?php

namespace App\Http\Requests;

class UpdateNhomRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ten_nhom' => 'sometimes|required|string|max:100',
        ];
    }
}
