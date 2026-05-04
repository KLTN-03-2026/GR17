<?php

namespace App\Http\Requests;

class UpdateHoaDonRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_nhom' => 'sometimes|required|string|exists:nhom,Ma_nhom',
            'loai_hoa_don' => 'sometimes|required|integer|in:0,1',
            'ma_doi_tuong' => 'sometimes|required|string|max:50',
            'tong_tien' => 'sometimes|required|numeric|min:0',
            'trang_thai_thanh_toan' => 'sometimes|required|integer|in:0,1,2',
            'ma_giao_dich' => 'nullable|string|max:100',
        ];
    }

    protected function getErrorMessage()
    {
        return 'Du lieu cap nhat hoa don khong hop le';
    }
}
