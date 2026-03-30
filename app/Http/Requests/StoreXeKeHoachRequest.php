<?php

namespace App\Http\Requests;

class StoreXeKeHoachRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_ke_hoach' => 'required|string|exists:ke_hoach,ma_ke_hoach',
            'ma_xe' => 'required|string|exists:xe,ma_xe',
            'so_luong' => 'required|integer|min:1',
            'so_ngay' => 'required|integer|min:1',
            'tong_tien' => 'required|numeric|min:0',
        ];
    }
}
