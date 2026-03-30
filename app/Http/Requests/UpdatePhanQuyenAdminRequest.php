<?php

namespace App\Http\Requests;

class UpdatePhanQuyenAdminRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_chuc_nang' => 'sometimes|required|string|exists:chuc_nang,ma_chuc_nang',
            'ma_chuc_vu' => 'sometimes|required|string|exists:chuc_vu,ma_chuc_vu',
        ];
    }
}
