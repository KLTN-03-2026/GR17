<?php
namespace App\Http\Requests;

class AdminLoginRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email',
            'Mat_khau' => 'required'
        ];
    }
}
