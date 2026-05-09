<?php

namespace App\Http\Requests;

class KhachHangStoreByAdminRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'Ho_va_ten' => ['required', 'string', 'min:5', 'max:40', 'regex:/^[\pL\s]+$/u'],
            'Mat_khau' => 'required|string|min:8',
            'Email' => 'required|email|unique:khach_hang,Email',
            'Ngay_sinh' => 'required|date_format:d/m/Y',
            'Gioi_tinh' => 'required|boolean',
            'so_dien_thoai' => 'required|regex:/^0[0-9]{9}$/|unique:khach_hang,so_dien_thoai',
            'is_block' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'Ho_va_ten.regex' => 'Họ và tên chỉ được chứa chữ cái và khoảng trắng.',
            'Ngay_sinh.date_format' => 'Ngày sinh phải đúng định dạng dd/mm/YYYY.',
        ];
    }
}
