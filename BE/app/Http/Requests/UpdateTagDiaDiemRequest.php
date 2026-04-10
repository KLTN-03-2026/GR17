<?php

namespace App\Http\Requests;

class UpdateTagDiaDiemRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_dia_diem' => 'sometimes|required|string|exists:dia_diem,ma_dia_diem',
            'ma_tag' => 'sometimes|required|string|exists:tag,ma_tag',
        ];
    }
}
