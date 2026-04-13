<?php

namespace App\Http\Requests;

class UpdateChiTietTourRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_tour' => 'sometimes|required|string|max:10',
            'ma_dia_diem' => 'sometimes|required|string|max:10',
        ];
    }
}