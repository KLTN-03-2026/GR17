<?php

namespace App\Http\Requests;

class StoreChiTietTourRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_tour' => 'required|max:10',
            'ma_dia_diem' => 'required|max:10',
            'ngay_hanh_trinh' => 'nullable|integer|min:1',
            'thu_tu_hanh_trinh' => 'nullable|integer|min:1',
            'ghi_chu_hanh_trinh' => 'nullable|string|max:1000',
        ];
    }
}
