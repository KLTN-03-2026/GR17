<?php

namespace App\Http\Requests;

class AdminChangePasswordRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'current_password' => 'required',
            'new_password' => 'required|min:8'
        ];
    }
}
