<?php

namespace App\Http\Requests;

class StoreChucNangRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ten_chuc_nang' => 'required|string|max:100|unique:chuc_nang,ten_chuc_nang',
            'mo_ta' => 'nullable|string',
        ];
    }
}
