<?php

namespace App\Http\Requests;

class DoiTacChangeStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'is_block' => 'required|boolean',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Cập nhật trạng thái đối tác thất bại';
    }
}

