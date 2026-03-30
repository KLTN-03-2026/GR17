<?php

namespace App\Http\Requests;

class UpdateChucVuRequest extends BaseRequest
{
    public function rules(): array
    {
        $ma_chuc_vu = $this->route('ma_chuc_vu');
        return [
            'ten_chuc_vu' => 'sometimes|required|string|min:3|max:50|unique:chuc_vu,ten_chuc_vu,' . $ma_chuc_vu . ',ma_chuc_vu|regex:/^[a-zA-ZÀ-ỿ\s]+$/',
            'tinh_trang' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_chuc_vu.required' => 'Tên chức vụ không được để trống',
            'ten_chuc_vu.unique' => 'Tên chức vụ này đã tồn tại',
        ];
    }
}
