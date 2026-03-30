<?php

namespace App\Http\Requests;

class UpdateHoaDonStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'trang_thai_thanh_toan' => 'required|integer|in:0,1,2',
        ];
    }
}
