<?php

namespace App\Http\Requests;

class KhachHangUpdateProfileRequest extends BaseRequest
{
    public function rules()
    {
        $maKhachHang = $this->route('maKhachHang');
        return [
            'Ho_va_ten' => 'nullable|string|min:5|max:40|regex:/^[\\pL\\s]+$/u',
            'Ngay_sinh' => 'nullable|date_format:d/m/Y',
            'Gioi_tinh' => 'nullable|boolean',
            'so_dien_thoai' => 'nullable|regex:/^0[0-9]{9}$/|unique:khach_hang,so_dien_thoai,' . $maKhachHang . ',Ma_khach_hang',
            'Email' => 'nullable|email|unique:khach_hang,Email,' . $maKhachHang . ',Ma_khach_hang',
            'is_block' => 'nullable|boolean',
        ];
    }
}
