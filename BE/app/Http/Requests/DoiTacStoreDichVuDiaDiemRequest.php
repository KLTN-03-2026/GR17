<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DoiTacStoreDichVuDiaDiemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_dia_diem' => ['required', 'string', 'max:10', 'exists:dia_diem,ma_dia_diem'],
            'ten_dich_vu' => ['required', 'string', 'max:100'],
            'mo_ta' => ['nullable', 'string'],
            'so_nguoi_toi_da' => ['required', 'integer', 'min:1'],
            'gia' => ['required', 'numeric', 'min:0'],
            'trang_thai' => ['nullable', 'boolean'],
        ];
    }
}
