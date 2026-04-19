<?php

namespace App\Http\Requests;

class UpdateThanhVienNhomRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'Ma_nhom' => 'sometimes|required|string|exists:nhom,Ma_nhom',
            'Ma_khach_hang' => 'sometimes|required|string|exists:khach_hang,Ma_khach_hang',
            'vai_tro' => 'sometimes|required|integer|in:0,1',
        ];
    }

    protected function getErrorMessage()
    {
        return 'Du lieu cap nhat thanh vien nhom khong hop le';
    }
}
