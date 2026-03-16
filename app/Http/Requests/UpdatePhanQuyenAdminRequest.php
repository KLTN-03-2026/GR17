<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhanQuyenAdminRequest extends FormRequest
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
            'id_chuc_nang' => 'sometimes|required|integer|exists:chuc_nang,id_chuc_nang',
            'id_chuc_vu' => 'sometimes|required|integer|exists:chuc_vu,id_chuc_vu',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'id_chuc_nang.required' => 'Chức năng không được để trống',
            'id_chuc_nang.integer' => 'Chức năng phải là số nguyên',
            'id_chuc_nang.exists' => 'Chức năng không tồn tại',
            'id_chuc_vu.required' => 'Chức vụ không được để trống',
            'id_chuc_vu.integer' => 'Chức vụ phải là số nguyên',
            'id_chuc_vu.exists' => 'Chức vụ không tồn tại',
        ];
    }
}
