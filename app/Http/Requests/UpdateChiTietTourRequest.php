<?php

namespace App\Http\Requests;

class UpdateChiTietTourRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_tour' => 'sometimes|required|max:10',
            'ma_dia_diem' => 'sometimes|required|max:10',
            'ngay_hanh_trinh' => 'sometimes|nullable|integer|min:1',
            'thu_tu_hanh_trinh' => 'sometimes|nullable|integer|min:1',
            'ghi_chu_hanh_trinh' => 'sometimes|nullable|string|max:1000',
        ];
    }
}
