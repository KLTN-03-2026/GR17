<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDichVuDiaDiemRequest extends FormRequest
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
        $madichuvu = $this->route('ma_dich_vu');

        return [
            'ma_dia_diem' => 'sometimes|exists:dia_diem,ma_dia_diem',
            'ten_dich_vu' => 'sometimes|min:3|max:100',
            'mo_ta' => 'nullable|string',
            'so_nguoi_toi_da' => 'sometimes|integer|min:1',
            'gia' => 'sometimes|numeric|min:0',
            'trang_thai' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'ma_dia_diem.exists' => 'Địa điểm không tồn tại',
            'ten_dich_vu.min' => 'Tên dịch vụ phải có ít nhất 3 ký tự',
            'ten_dich_vu.max' => 'Tên dịch vụ không được vượt quá 100 ký tự',
            'so_nguoi_toi_da.integer' => 'Số người tối đa phải là số nguyên',
            'so_nguoi_toi_da.min' => 'Số người tối đa phải ít nhất từ 1',
            'gia.numeric' => 'Giá phải là số',
            'gia.min' => 'Giá phải lớn hơn hoặc bằng 0',
            'trang_thai.boolean' => 'Trạng thái phải là true (1) hoặc false (0)',
        ];
    }
}
