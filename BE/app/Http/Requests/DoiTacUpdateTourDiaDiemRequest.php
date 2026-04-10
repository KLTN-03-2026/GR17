<?php

namespace App\Http\Requests;

class DoiTacUpdateTourDiaDiemRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ngay_hanh_trinh' => 'nullable|integer|min:1',
            'thu_tu_hanh_trinh' => 'required|integer|min:1',
            'ghi_chu_hanh_trinh' => 'nullable|string|max:1000',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Cập nhật hành trình tour thất bại';
    }
}

