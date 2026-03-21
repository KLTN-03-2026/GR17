<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateXeKeHoachRequest extends FormRequest
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
            'ma_ke_hoach' => 'sometimes|required|string|exists:ke_hoach,ma_ke_hoach',
            'ma_xe' => 'sometimes|required|string|exists:xe,ma_xe',
            'so_luong' => 'sometimes|required|integer|min:1',
            'so_ngay' => 'sometimes|required|integer|min:1',
            'tong_tien' => 'sometimes|required|numeric|min:0',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'ma_ke_hoach.required' => 'Kế hoạch không được để trống',
            'ma_ke_hoach.string' => 'Kế hoạch phải là chuỗi ký tự',
            'ma_ke_hoach.exists' => 'Kế hoạch không tồn tại',
            'ma_xe.required' => 'Xe không được để trống',
            'ma_xe.string' => 'Xe phải là chuỗi ký tự',
            'ma_xe.exists' => 'Xe không tồn tại',
            'so_luong.required' => 'Số lượng không được để trống',
            'so_luong.integer' => 'Số lượng phải là số nguyên',
            'so_luong.min' => 'Số lượng phải ít nhất là 1',
            'so_ngay.required' => 'Số ngày không được để trống',
            'so_ngay.integer' => 'Số ngày phải là số nguyên',
            'so_ngay.min' => 'Số ngày phải ít nhất là 1',
            'tong_tien.required' => 'Tổng tiền không được để trống',
            'tong_tien.numeric' => 'Tổng tiền phải là số',
            'tong_tien.min' => 'Tổng tiền không được âm',
        ];
    }
}
