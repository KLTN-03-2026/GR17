<?php

namespace App\Http\Requests;

class StoreHoaDonRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_nhom' => 'required|string|exists:nhom,Ma_nhom',
            'loai_hoa_don' => 'required|integer|in:0,1',
            'ma_doi_tuong' => 'required|string|max:50',
            'tong_tien' => 'required|numeric|min:0',
            'trang_thai_thanh_toan' => 'required|integer|in:0,1,2',
            'ma_giao_dich' => 'nullable|string|max:100',
        ];
    }

    protected function getErrorMessage()
    {
        return 'Du lieu hoa don khong hop le';
    }
}
