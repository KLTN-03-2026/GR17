<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreDanhGiaKeHoachRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'Ma_khach_hang' => [
                'required',
                'string',
                'exists:khach_hang,Ma_khach_hang',
                Rule::unique('danh_gia_ke_hoach', 'Ma_khach_hang')
                    ->where(fn ($query) => $query->where('ma_dia_diem', $this->input('ma_dia_diem'))),
            ],
            'ma_dia_diem' => 'required|string|exists:dia_diem,ma_dia_diem',
            'so_sao' => 'required|integer|min:1|max:5',
            'noi_dung' => 'nullable|string|max:1000',
        ];
    }

    protected function getErrorMessage()
    {
        return 'Du lieu danh gia khong hop le';
    }
}
