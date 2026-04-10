<?php

namespace App\Http\Requests;

class DoiTacUpdateDiaDiemRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ten_dia_diem' => 'sometimes|string|min:5|max:100',
            'loai' => 'sometimes|integer|in:1,2,3',
            'dia_chi' => 'sometimes|string|max:255',
            'sdt' => 'sometimes|nullable|regex:/^0[0-9]{9}$/',
            'kinh_do' => 'sometimes|numeric|between:-180,180',
            'vi_do' => 'sometimes|numeric|between:-90,90',
            'gio_mo_cua' => 'sometimes|nullable|date_format:H:i',
            'gio_dong_cua' => 'sometimes|nullable|date_format:H:i',
            'gia_giao_dong' => 'sometimes|nullable|numeric|min:0',
            'hinh_anh' => 'sometimes|nullable|string|url',
            'mo_ta' => 'sometimes|nullable|string',
            'thoi_gian_tham_quan' => 'sometimes|nullable|string',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Cập nhật địa điểm đối tác thất bại';
    }
}

