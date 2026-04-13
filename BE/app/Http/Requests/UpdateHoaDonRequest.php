<?php

namespace App\Http\Requests;

class UpdateHoaDonRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_nhom' => 'sometimes|required|string',
            'loai_hoa_don' => 'sometimes|required|integer',
            'ma_doi_tuong' => 'sometimes|required|string',
            'tong_tien' => 'sometimes|required|numeric',
            'trang_thai_thanh_toan' => 'sometimes|required|integer',
            'ma_giao_dich' => 'nullable|string',
        ];
    }
}