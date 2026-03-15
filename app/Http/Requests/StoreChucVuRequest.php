<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChucVuRequest extends FormRequest
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
            'ten_chuc_vu' => 'required|string|min:3|max:50|unique:chuc_vu,ten_chuc_vu|regex:/^[a-zA-ZÀ-ỿ\s]+$/',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'ten_chuc_vu.required' => 'Tên chức vụ không được để trống',
            'ten_chuc_vu.string' => 'Tên chức vụ phải là chuỗi ký tự',
            'ten_chuc_vu.min' => 'Tên chức vụ phải có ít nhất 3 ký tự',
            'ten_chuc_vu.max' => 'Tên chức vụ không được vượt quá 50 ký tự',
            'ten_chuc_vu.unique' => 'Tên chức vụ này đã tồn tại',
            'ten_chuc_vu.regex' => 'Tên chức vụ chỉ được chứa chữ cái',
        ];
    }
}
