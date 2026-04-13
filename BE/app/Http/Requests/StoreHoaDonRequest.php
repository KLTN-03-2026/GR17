<?php

namespace App\Http\Requests;

class StoreHoaDonRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_nhom' => 'required|string',
            'loai_hoa_don' => 'required|integer',
            'ma_doi_tuong' => 'required|string',
            'tong_tien' => 'required|numeric',
            'trang_thai_thanh_toan' => 'required|integer',
            'ma_giao_dich' => 'nullable|string',
        ];
    }
}