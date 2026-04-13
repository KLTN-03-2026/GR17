<?php

namespace App\Http\Requests;

class StoreChiTietTourRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_tour' => 'required|string|max:10',
            'ma_dia_diem' => 'required|string|max:10',
        ];
    }
}