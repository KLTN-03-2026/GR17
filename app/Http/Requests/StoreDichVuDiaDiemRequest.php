<?php

namespace App\Http\Requests;

class StoreDichVuDiaDiemRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_dich_vu' => 'required|unique:dich_vu_dia_diem|max:10',
            'ma_dia_diem' => 'required|exists:dia_diem,ma_dia_diem',
            'ten_dich_vu' => 'required|min:3|max:100',
            'mo_ta' => 'nullable|string',
            'so_nguoi_toi_da' => 'required|integer|min:1',
            'gia' => 'required|numeric|min:0',
            'trang_thai' => 'required|boolean',
        ];
    }
}
