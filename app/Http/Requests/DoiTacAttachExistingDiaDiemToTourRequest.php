<?php

namespace App\Http\Requests;

class DoiTacAttachExistingDiaDiemToTourRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_dia_diem' => 'required|string|max:10',
            'ngay_hanh_trinh' => 'nullable|integer|min:1',
            'thu_tu_hanh_trinh' => 'nullable|integer|min:1',
            'ghi_chu_hanh_trinh' => 'nullable|string|max:1000',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Gắn địa điểm có sẵn vào tour thất bại';
    }
}

