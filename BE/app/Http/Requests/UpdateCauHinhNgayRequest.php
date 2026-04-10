<?php

namespace App\Http\Requests;

class UpdateCauHinhNgayRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'loai_ngay_le' => 'sometimes|required|integer|in:1,2,3',
            'ten_ngay_le' => 'nullable|string|max:255',
            'ngay' => 'sometimes|required|date'
        ];
    }
}
