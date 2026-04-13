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
>>>>>>> f18e65568abb7ef9897b859e68d91f1b38eb1e05
