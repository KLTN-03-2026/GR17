<?php

namespace App\Http\Requests;

class KhachHangChangePasswordRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ];
    }
}
