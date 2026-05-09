<?php

namespace App\Http\Requests;

class UpdateDiaDiemRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ten_dia_diem' => 'nullable|string|min:5|max:100',
            'loai' => 'nullable|integer|in:1,2,3',
            'dia_chi' => 'nullable|string|max:255',
            'sdt' => 'nullable|regex:/^0[0-9]{9}$/',
            'kinh_do' => 'nullable|numeric|between:-180,180',
            'vi_do' => 'nullable|numeric|between:-90,90',
            'gio_mo_cua' => 'nullable|date_format:H:i',
            'gio_dong_cua' => 'nullable|date_format:H:i',
            'gia_giao_dong' => 'nullable|numeric|min:0',
            'hinh_anh' => 'nullable|string|url',
            'mo_ta' => 'nullable|string',
            'thoi_gian_tham_quan' => 'nullable|string',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Cập nhật địa điểm thất bại';
    }
}
