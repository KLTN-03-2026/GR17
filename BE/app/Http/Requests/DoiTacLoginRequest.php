<?php

namespace App\Http\Requests;

class DoiTacLoginRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'mat_khau' => 'required|string',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Đăng nhập đối tác thất bại';
    }
}

