<?php

namespace App\Http\Requests;

class UpdateKeHoachRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_khach_hang' => 'sometimes|exists:khach_hang,Ma_khach_hang',
            'ma_nhom' => 'sometimes|exists:nhom,Ma_nhom',
            'ten_ke_hoach' => 'sometimes|min:3|max:100',
            'so_nguoi' => 'sometimes|integer|min:1',
            'ngay_bat_dau' => 'sometimes|date_format:Y-m-d',
            'ngay_ket_thuc' => 'sometimes|date_format:Y-m-d|after_or_equal:ngay_bat_dau',
            'ngan_sach_du_kien' => 'sometimes|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'sometimes|boolean',
        ];
    }
}
