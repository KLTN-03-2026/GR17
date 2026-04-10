<?php

namespace App\Http\Requests;

class UpdateChucNangRequest extends BaseRequest
{
    public function rules(): array
    {
        $ma_chuc_nang = $this->route('ma_chuc_nang');
        return [
            'ten_chuc_nang' => 'sometimes|required|string|max:100|unique:chuc_nang,ten_chuc_nang,' . $ma_chuc_nang . ',ma_chuc_nang',
            'mo_ta' => 'nullable|string',
        ];
    }
}
