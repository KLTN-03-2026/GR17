<?php

namespace App\Http\Requests;

class StoreTourKhoiHanhRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_tour' => 'required|max:10',
            'so_tien' => 'required|numeric|min:0',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'so_cho' => 'nullable|integer',
            'tinh_trang' => 'nullable|boolean'
        ];
    }
}
