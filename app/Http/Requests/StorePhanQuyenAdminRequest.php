<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhanQuyenAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'ma_chuc_nang' => 'required|string|exists:chuc_nang,ma_chuc_nang',
            'ma_chuc_vu' => 'required|string|exists:chuc_vu,ma_chuc_vu',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'ma_chuc_nang.required' => 'Chức năng không được để trống',
            'ma_chuc_nang.string' => 'Chức năng phải là chuỗi ký tự',
            'ma_chuc_nang.exists' => 'Chức năng không tồn tại',
            'ma_chuc_vu.required' => 'Chức vụ không được để trống',
            'ma_chuc_vu.string' => 'Chức vụ phải là chuỗi ký tự',
            'ma_chuc_vu.exists' => 'Chức vụ không tồn tại',
        ];
    }
}
