<?php

namespace App\Http\Requests;

class StoreDanhGiaKeHoachRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'Ma_khach_hang' => 'required|string|exists:khach_hang,Ma_khach_hang',
            'ma_dia_diem' => 'required|string|exists:dia_diem,ma_dia_diem',
            'so_sao' => 'required|integer|min:1|max:5',
            'noi_dung' => 'nullable|string|max:1000',
        ];
    }
}
