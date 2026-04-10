<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateDanhSachYeuThichRequest extends BaseRequest
{
    public function rules(): array
    {
        $danhSachId = $this->route('ma_danh_sach_ua_thich');
        $maKhachHang = $this->input('ma_khach_hang'); // This will need to be passed or resolved before validation if needed, but here it's used in the controller.

        return [
            'ma_dia_diem' => [
                'required',
                'exists:dia_diem,ma_dia_diem',
                // Note: The unique rule here depends on ma_khach_hang which is resolved in the controller.
                // For simplicity and since it's a refactor of existing manual logic, 
                // I'll keep the complex unique check in the controller or assume it's passed.
            ],
        ];
    }
}
