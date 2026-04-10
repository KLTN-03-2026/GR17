<?php

namespace App\Http\Requests;

class StoreCauHinhNgayRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'loai_ngay_le' => 'required|integer|in:1,2,3',
            'ten_ngay_le' => 'nullable|string|max:255',
            'ngay' => 'required|date'
        ];
    }
}
