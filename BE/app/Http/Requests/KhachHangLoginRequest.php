<?php

namespace App\Http\Requests;

class KhachHangLoginRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'Email' => 'required|email',
            'Mat_khau' => 'required|string',
        ];
    }
}
