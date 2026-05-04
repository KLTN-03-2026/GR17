<?php

namespace App\Http\Requests;

class UpdateHoaDonStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'trang_thai_thanh_toan' => 'required|integer|in:0,1,2',
            'ma_giao_dich' => 'nullable|string|max:100',
        ];
    }

    protected function getErrorMessage()
    {
        return 'Du lieu trang thai hoa don khong hop le';
    }
}
