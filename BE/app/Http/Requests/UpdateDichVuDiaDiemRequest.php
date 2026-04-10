<?php

namespace App\Http\Requests;

class UpdateDichVuDiaDiemRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_dia_diem' => 'sometimes|exists:dia_diem,ma_dia_diem',
            'ten_dich_vu' => 'sometimes|min:3|max:100',
            'mo_ta' => 'nullable|string',
            'so_nguoi_toi_da' => 'sometimes|integer|min:1',
            'gia' => 'sometimes|numeric|min:0',
            'trang_thai' => 'sometimes|boolean',
        ];
    }
}
