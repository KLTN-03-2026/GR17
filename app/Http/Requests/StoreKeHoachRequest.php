<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKeHoachRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'ma_ke_hoach' => 'required|unique:ke_hoach|max:10',
            'ma_khach_hang' => 'sometimes|exists:khach_hang,Ma_khach_hang',
            'ma_nhom' => 'required|exists:nhom,Ma_nhom',
            'ten_ke_hoach' => 'required|min:3|max:100',
            'so_nguoi' => 'required|integer|min:1',
            'ngay_bat_dau' => 'required|date_format:Y-m-d',
            'ngay_ket_thuc' => 'required|date_format:Y-m-d|after:ngay_bat_dau',
            'ngan_sach_du_kien' => 'required|numeric|min:0',
            'trang_thai' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'ma_ke_hoach.required' => 'Mã kế hoạch không được để trống',
            'ma_ke_hoach.unique' => 'Mã kế hoạch đã tồn tại',
            'ma_ke_hoach.max' => 'Mã kế hoạch không được vượt quá 10 ký tự',
            'ma_nhom.required' => 'Mã nhóm không được để trống',
            'ma_nhom.exists' => 'Nhóm không tồn tại',
            'ten_ke_hoach.required' => 'Tên kế hoạch không được để trống',
            'ten_ke_hoach.min' => 'Tên kế hoạch phải có ít nhất 3 ký tự',
            'ten_ke_hoach.max' => 'Tên kế hoạch không được vượt quá 100 ký tự',
            'so_nguoi.required' => 'Số người không được để trống',
            'so_nguoi.integer' => 'Số người phải là số nguyên',
            'so_nguoi.min' => 'Số người phải ít nhất từ 1',
            'ngay_bat_dau.required' => 'Ngày bắt đầu không được để trống',
            'ngay_bat_dau.date_format' => 'Ngày bắt đầu phải ở định dạng Y-m-d',
            'ngay_ket_thuc.required' => 'Ngày kết thúc không được để trống',
            'ngay_ket_thuc.date_format' => 'Ngày kết thúc phải ở định dạng Y-m-d',
            'ngay_ket_thuc.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            'ngan_sach_du_kien.required' => 'Ngân sách dự kiến không được để trống',
            'ngan_sach_du_kien.numeric' => 'Ngân sách dự kiến phải là số',
            'ngan_sach_du_kien.min' => 'Ngân sách dự kiến phải lớn hơn hoặc bằng 0',
            'trang_thai.required' => 'Trạng thái không được để trống',
            'trang_thai.boolean' => 'Trạng thái phải là true (1) hoặc false (0)',
        ];
    }
}
