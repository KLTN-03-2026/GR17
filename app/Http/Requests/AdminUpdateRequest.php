<?php

namespace App\Http\Requests;

class AdminUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'Ho_va_ten' => 'sometimes|string|max:100',
            'Email' => 'sometimes|email|unique:admin,Email,' . $this->route('id') . ',Ma_admin',
            'so_dien_thoai' => 'sometimes|string|max:15',
            'is_block' => 'sometimes|boolean',
        ];
    }
}
