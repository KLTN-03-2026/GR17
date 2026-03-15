<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreXeRequest extends FormRequest
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
            'ten_xe' => 'required|string|min:3|max:100',
            'loai_xe' => 'required|string|min:3|max:50',
            'so_cho' => 'required|integer|min:1',
            'gia_theo_ngay' => 'required|numeric|min:0',
            'tong_so_xe' => 'required|integer|min:0',
            'mo_ta' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'ten_xe.required' => 'Tên xe không được để trống',
            'ten_xe.string' => 'Tên xe phải là chuỗi ký tự',
            'ten_xe.min' => 'Tên xe phải có ít nhất 3 ký tự',
            'ten_xe.max' => 'Tên xe không được vượt quá 100 ký tự',
            'loai_xe.required' => 'Loại xe không được để trống',
            'loai_xe.string' => 'Loại xe phải là chuỗi ký tự',
            'loai_xe.min' => 'Loại xe phải có ít nhất 3 ký tự',
            'loai_xe.max' => 'Loại xe không được vượt quá 50 ký tự',
            'so_cho.required' => 'Số chỗ không được để trống',
            'so_cho.integer' => 'Số chỗ phải là số nguyên',
            'so_cho.min' => 'Số chỗ phải ít nhất là 1',
            'gia_theo_ngay.required' => 'Giá theo ngày không được để trống',
            'gia_theo_ngay.numeric' => 'Giá theo ngày phải là số',
            'gia_theo_ngay.min' => 'Giá theo ngày không được âm',
            'tong_so_xe.required' => 'Tổng số xe không được để trống',
            'tong_so_xe.integer' => 'Tổng số xe phải là số nguyên',
            'tong_so_xe.min' => 'Tổng số xe không được âm',
            'mo_ta.max' => 'Mô tả không được vượt quá 500 ký tự',
        ];
    }
}
