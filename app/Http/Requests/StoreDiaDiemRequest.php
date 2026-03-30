<?php

namespace App\Http\Requests;

class StoreDiaDiemRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_dia_diem' => 'required|string|unique:dia_diem|max:10',
            'ten_dia_diem' => 'required|string|min:5|max:100',
            'loai' => 'required|integer|in:1,2,3',
            'dia_chi' => 'required|string|max:255',
            'sdt' => 'nullable|regex:/^0[0-9]{9}$/',
            'kinh_do' => 'required|numeric|between:-180,180',
            'vi_do' => 'required|numeric|between:-90,90',
            'gio_mo_cua' => 'nullable|date_format:H:i',
            'gio_dong_cua' => 'nullable|date_format:H:i',
            'gia_giao_dong' => 'nullable|numeric|min:0',
            'hinh_anh' => 'nullable|string|url',
            'mo_ta' => 'nullable|string',
            'thoi_gian_tham_quan' => 'nullable|string',
        ];
    }

    protected function getErrorMessage()
    {
        return 'Thêm địa điểm thất bại';
    }
}
