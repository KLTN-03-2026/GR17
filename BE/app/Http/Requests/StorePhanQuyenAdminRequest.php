<?php

namespace App\Http\Requests;

class StorePhanQuyenAdminRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_chuc_nang' => 'required|string|exists:chuc_nang,ma_chuc_nang',
            'ma_chuc_vu' => 'required|string|exists:chuc_vu,ma_chuc_vu',
        ];
    }
}
