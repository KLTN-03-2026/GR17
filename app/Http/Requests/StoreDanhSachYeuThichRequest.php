<?php

namespace App\Http\Requests;

class StoreDanhSachYeuThichRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_dia_diem' => 'required|exists:dia_diem,ma_dia_diem',
            'ma_danh_sach_ua_thich' => 'nullable|string',
        ];
    }
}
