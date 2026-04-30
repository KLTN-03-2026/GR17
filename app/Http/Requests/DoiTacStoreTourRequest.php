<?php

namespace App\Http\Requests;

class DoiTacStoreTourRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ten_tour' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|string',
            'so_ngay' => 'nullable|integer|min:1',
            'so_nguoi' => 'nullable|integer|min:1',
            'ma_tag' => 'nullable|string|max:50',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Thêm tour đối tác thất bại';
    }
}

