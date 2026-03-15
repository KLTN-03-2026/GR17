<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreXeKeHoachRequest extends FormRequest
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
            'id_ke_hoach' => 'required|integer|exists:ke_hoach,id_ke_hoach',
            'id_xe' => 'required|integer|exists:xe,id_xe',
            'so_luong' => 'required|integer|min:1',
            'so_ngay' => 'required|integer|min:1',
            'tong_tien' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'id_ke_hoach.required' => 'Kế hoạch không được để trống',
            'id_ke_hoach.integer' => 'Kế hoạch phải là số nguyên',
            'id_ke_hoach.exists' => 'Kế hoạch không tồn tại',
            'id_xe.required' => 'Xe không được để trống',
            'id_xe.integer' => 'Xe phải là số nguyên',
            'id_xe.exists' => 'Xe không tồn tại',
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
