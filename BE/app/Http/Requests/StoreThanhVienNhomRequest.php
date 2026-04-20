<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreThanhVienNhomRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'Ma_nhom' => 'required|string|exists:nhom,Ma_nhom',
            'Ma_khach_hang' => [
                'required',
                'string',
                'exists:khach_hang,Ma_khach_hang',
                Rule::unique('thanh_vien_nhom', 'Ma_khach_hang')->where(
                    fn ($query) => $query->where('Ma_nhom', $this->input('Ma_nhom'))
                ),
            ],
            'vai_tro' => 'required|integer|in:0,1',
        ];
    }

    protected function getErrorMessage()
    {
        return 'Du lieu them thanh vien nhom khong hop le';
    }
}
