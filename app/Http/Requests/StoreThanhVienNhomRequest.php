<?php

namespace App\Http\Requests;

class StoreThanhVienNhomRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'Ma_thanh_vien' => 'sometimes|string|unique:thanh_vien_nhom|max:20',
            'Ma_nhom' => 'required|string|exists:nhom,Ma_nhom',
            'Ma_khach_hang' => 'required|string|exists:khach_hang,Ma_khach_hang',
            'vai_tro' => 'nullable|integer|in:0,1',
        ];
    }
}
