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
>>>>>>> f18e65568abb7ef9897b859e68d91f1b38eb1e05
