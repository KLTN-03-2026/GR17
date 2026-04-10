<?php

namespace App\Http\Requests;

class UpdateThanhVienNhomRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'vai_tro' => 'sometimes|required|integer|in:0,1',
        ];
    }
}
