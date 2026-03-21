<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDichVuDiaDiemRequest extends FormRequest
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
            'ma_dich_vu' => 'required|unique:dich_vu_dia_diem|max:10',
            'ma_dia_diem' => 'required|exists:dia_diem,ma_dia_diem',
            'ten_dich_vu' => 'required|min:3|max:100',
            'mo_ta' => 'nullable|string',
            'so_nguoi_toi_da' => 'required|integer|min:1',
            'gia' => 'required|numeric|min:0',
            'trang_thai' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'ma_dich_vu.required' => 'Mã dịch vụ không được để trống',
            'ma_dich_vu.unique' => 'Mã dịch vụ đã tồn tại',
            'ma_dich_vu.max' => 'Mã dịch vụ không được vượt quá 10 ký tự',
            'ma_dia_diem.required' => 'Mã địa điểm không được để trống',
            'ma_dia_diem.exists' => 'Địa điểm không tồn tại',
            'ten_dich_vu.required' => 'Tên dịch vụ không được để trống',
            'ten_dich_vu.min' => 'Tên dịch vụ phải có ít nhất 3 ký tự',
            'ten_dich_vu.max' => 'Tên dịch vụ không được vượt quá 100 ký tự',
            'so_nguoi_toi_da.required' => 'Số người tối đa không được để trống',
            'so_nguoi_toi_da.integer' => 'Số người tối đa phải là số nguyên',
            'so_nguoi_toi_da.min' => 'Số người tối đa phải ít nhất từ 1',
            'gia.required' => 'Giá không được để trống',
            'gia.numeric' => 'Giá phải là số',
            'gia.min' => 'Giá phải lớn hơn hoặc bằng 0',
            'trang_thai.required' => 'Trạng thái không được để trống',
            'trang_thai.boolean' => 'Trạng thái phải là true (1) hoặc false (0)',
        ];
    }
}
