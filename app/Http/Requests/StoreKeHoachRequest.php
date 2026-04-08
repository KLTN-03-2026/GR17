<?php

namespace App\Http\Requests;

class StoreKeHoachRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_khach_hang' => 'sometimes|exists:khach_hang,Ma_khach_hang',
            'ma_nhom' => 'required|exists:nhom,Ma_nhom',
            'ten_ke_hoach' => 'required|min:3|max:100',
            'so_nguoi' => 'required|integer|min:1',
            'ngay_bat_dau' => 'required|date_format:Y-m-d',
            'ngay_ket_thuc' => 'required|date_format:Y-m-d|after:ngay_bat_dau',
            'ngan_sach_du_kien' => 'required|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'required|boolean',
        ];
    }
}
