<?php

namespace App\Http\Requests;

class UpdateTourRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ten_tour' => 'sometimes|required|string|max:255',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|string',
            'so_ngay' => 'nullable|integer',
            'so_nguoi' => 'nullable|integer',
            'ma_tag' => 'nullable|string'
        ];
    }
}
