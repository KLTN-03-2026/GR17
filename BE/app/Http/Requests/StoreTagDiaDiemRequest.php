<?php

namespace App\Http\Requests;

class StoreTagDiaDiemRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_tag_dia_diem' => 'required|string|unique:tag_dia_diem|max:10',
            'ma_dia_diem' => 'required|string|exists:dia_diem,ma_dia_diem',
            'ma_tag' => 'required|string|exists:tag,ma_tag',
        ];
    }
}
