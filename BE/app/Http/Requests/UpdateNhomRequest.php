<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNhomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_nhom' => ['sometimes', 'required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'ten_nhom.required' => 'Ten nhom la bat buoc.',
            'ten_nhom.string' => 'Ten nhom khong hop le.',
            'ten_nhom.max' => 'Ten nhom khong duoc vuot qua 100 ky tu.',
        ];
    }
}
