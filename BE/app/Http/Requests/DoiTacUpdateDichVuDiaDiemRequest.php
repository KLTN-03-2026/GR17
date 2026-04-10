<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DoiTacUpdateDichVuDiaDiemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_dich_vu' => ['sometimes', 'required', 'string', 'max:100'],
            'mo_ta' => ['nullable', 'string'],
            'so_nguoi_toi_da' => ['sometimes', 'required', 'integer', 'min:1'],
            'gia' => ['sometimes', 'required', 'numeric', 'min:0'],
            'trang_thai' => ['nullable', 'boolean'],
        ];
    }
}
