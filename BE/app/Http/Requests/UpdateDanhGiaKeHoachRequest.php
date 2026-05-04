<?php

namespace App\Http\Requests;

class UpdateDanhGiaKeHoachRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'so_sao' => 'sometimes|required|integer|min:1|max:5',
            'noi_dung' => 'nullable|string|max:1000',
        ];
    }

    protected function getErrorMessage()
    {
        return 'Du lieu cap nhat danh gia khong hop le';
    }
}
