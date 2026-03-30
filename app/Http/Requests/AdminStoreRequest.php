<?php

namespace App\Http\Requests;

class AdminStoreRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'Ho_va_ten' => 'required|min:5|max:40',
            'Mat_khau' => 'required|min:8',
            'Email' => 'required|email|unique:admins',
            'Ngay_sinh' => 'required|date_format:Y-m-d',
            'Gioi_tinh' => 'required|boolean',
            'ma_chuc_vu' => 'required',
            'so_dien_thoai' => 'required|unique:admins|regex:/^0[0-9]{9}$/',
        ];
    }

    protected function getErrorMessage()
    {
        return 'Thêm quản trị viên thất bại';
    }
}
