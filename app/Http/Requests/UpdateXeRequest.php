<?php

namespace App\Http\Requests;

class UpdateXeRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ten_xe' => 'sometimes|required|string|min:3|max:100',
            'loai_xe' => 'sometimes|required|string|min:3|max:50',
            'so_cho' => 'sometimes|required|integer|min:1',
            'gia_theo_ngay' => 'sometimes|required|numeric|min:0',
            'tong_so_xe' => 'sometimes|required|integer|min:0',
            'mo_ta' => 'nullable|string|max:500',
            'trang_thai' => 'sometimes|required|in:0,1',
        ];
    }
}
