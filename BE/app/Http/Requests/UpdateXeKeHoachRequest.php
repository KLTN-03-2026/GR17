<?php

namespace App\Http\Requests;

class UpdateXeKeHoachRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_ke_hoach' => 'sometimes|required|string|exists:ke_hoach,ma_ke_hoach',
            'ma_xe' => 'sometimes|required|string|exists:xe,ma_xe',
            'so_luong' => 'sometimes|required|integer|min:1',
            'so_ngay' => 'sometimes|required|integer|min:1',
            'tong_tien' => 'sometimes|required|numeric|min:0',
        ];
    }
}
