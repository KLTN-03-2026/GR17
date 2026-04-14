<?php

namespace App\Http\Requests;

class CreateTourQrPaymentRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_thoi_gian_tour' => 'required|string|max:10',
            'thong_tin_nguoi_dat' => 'required|array',
            'thong_tin_nguoi_dat.ho_ten' => 'required|string|max:120',
            'thong_tin_nguoi_dat.so_dien_thoai' => 'required|string|max:20',
            'thong_tin_nguoi_dat.email' => 'required|email|max:255',
            'thong_tin_nguoi_dat.dia_chi' => 'nullable|string|max:255',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Thông tin checkout không hợp lệ.';
    }
}
