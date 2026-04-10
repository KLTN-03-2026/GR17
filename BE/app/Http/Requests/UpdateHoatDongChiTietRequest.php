<?php

namespace App\Http\Requests;

class UpdateHoatDongChiTietRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_ke_hoach' => 'sometimes|required|exists:ke_hoach,ma_ke_hoach',
            'ma_nhom' => 'sometimes|required|exists:nhom,Ma_nhom',
            'ma_dia_diem' => 'sometimes|required|exists:dia_diem,ma_dia_diem',
            'gio_bat_dau' => 'sometimes|required|date_format:H:i',
            'gio_ket_thuc' => 'sometimes|required|date_format:H:i|after:gio_bat_dau',
            'ngay_cu_the' => 'sometimes|required|date_format:Y-m-d',
        ];
    }
}
