<?php

namespace App\Http\Requests;

class StoreHoatDongChiTietRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_ke_hoach' => 'required|exists:ke_hoach,ma_ke_hoach',
            'ma_nhom' => 'required|exists:nhom,Ma_nhom',
            'ma_dia_diem' => 'required|exists:dia_diem,ma_dia_diem',
            'gio_bat_dau' => 'required|date_format:H:i',
            'gio_ket_thuc' => 'required|date_format:H:i|after:gio_bat_dau',
            'ngay_cu_the' => 'required|date_format:Y-m-d',
        ];
    }
}
