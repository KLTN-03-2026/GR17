<?php

namespace App\Http\Requests;

class StoreXeRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ten_xe' => 'required|string|min:3|max:100',
            'loai_xe' => 'required|string|min:3|max:50',
            'so_cho' => 'required|integer|min:1',
            'gia_theo_ngay' => 'required|numeric|min:0',
            'tong_so_xe' => 'required|integer|min:0',
            'mo_ta' => 'nullable|string|max:500',
        ];
    }
}
