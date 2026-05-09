<?php

namespace App\Http\Requests;

class DoiTacUpdateTourRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ten_tour' => 'sometimes|required|string|max:255',
            'mo_ta' => 'sometimes|nullable|string',
            'hinh_anh' => 'sometimes|nullable|string',
            'so_ngay' => 'sometimes|nullable|integer|min:1',
            'so_nguoi' => 'sometimes|nullable|integer|min:1',
            'ma_tag' => 'sometimes|nullable|string|max:50',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Cập nhật tour đối tác thất bại';
    }
}

