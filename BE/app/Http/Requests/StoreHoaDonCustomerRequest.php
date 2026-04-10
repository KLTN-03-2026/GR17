<?php

namespace App\Http\Requests;

class StoreHoaDonCustomerRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_hoa_don' => 'required|unique:hoa_don,ma_hoa_don|max:10',
            'ma_nhom' => 'required|exists:nhom,Ma_nhom|max:20',
            'loai_hoa_don' => 'required|integer|in:0,1',
            'ma_doi_tuong' => 'required|string',
            'tong_tien' => 'required|numeric|min:0',
            'ma_giao_dich' => 'nullable|string'
        ];
    }
}
