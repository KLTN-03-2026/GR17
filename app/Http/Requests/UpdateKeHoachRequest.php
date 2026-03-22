<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKeHoachRequest extends FormRequest
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
            'ma_khach_hang' => 'sometimes|exists:khach_hang,Ma_khach_hang',
            'ma_nhom' => 'sometimes|exists:nhom,Ma_nhom',
            'ten_ke_hoach' => 'sometimes|min:3|max:100',
            'so_nguoi' => 'sometimes|integer|min:1',
            'ngay_bat_dau' => 'sometimes|date_format:Y-m-d',
            'ngay_ket_thuc' => 'sometimes|date_format:Y-m-d|after_or_equal:ngay_bat_dau',
            'ngan_sach_du_kien' => 'sometimes|numeric|min:0',
            'trang_thai' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'ma_nhom.exists' => 'Nhóm không tồn tại',
            'ten_ke_hoach.min' => 'Tên kế hoạch phải có ít nhất 3 ký tự',
            'ten_ke_hoach.max' => 'Tên kế hoạch không được vượt quá 100 ký tự',
            'so_nguoi.integer' => 'Số người phải là số nguyên',
            'so_nguoi.min' => 'Số người phải ít nhất từ 1',
            'ngay_bat_dau.date_format' => 'Ngày bắt đầu phải ở định dạng Y-m-d',
            'ngay_ket_thuc.date_format' => 'Ngày kết thúc phải ở định dạng Y-m-d',
            'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
            'ngan_sach_du_kien.numeric' => 'Ngân sách dự kiến phải là số',
            'ngan_sach_du_kien.min' => 'Ngân sách dự kiến phải lớn hơn hoặc bằng 0',
            'trang_thai.boolean' => 'Trạng thái phải là true (1) hoặc false (0)',
        ];
    }
}
