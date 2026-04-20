<?php

namespace App\Http\Requests;

use App\Models\ThanhVienNhom;
use Illuminate\Validation\Rule;

class UpdateThanhVienNhomRequest extends BaseRequest
{
    public function rules(): array
    {
        $memberId = $this->route('id');
        $currentGroupId = ThanhVienNhom::query()
            ->where('Ma_thanh_vien', $memberId)
            ->value('Ma_nhom');
        $groupId = $this->input('Ma_nhom', $currentGroupId);

        return [
            'Ma_nhom' => 'sometimes|required|string|exists:nhom,Ma_nhom',
            'Ma_khach_hang' => [
                'sometimes',
                'required',
                'string',
                'exists:khach_hang,Ma_khach_hang',
                Rule::unique('thanh_vien_nhom', 'Ma_khach_hang')
                    ->ignore($memberId, 'Ma_thanh_vien')
                    ->where(fn ($query) => $query->where('Ma_nhom', $groupId)),
            ],
            'vai_tro' => 'sometimes|required|integer|in:0,1',
        ];
    }

    protected function getErrorMessage()
    {
        return 'Du lieu cap nhat thanh vien nhom khong hop le';
    }
}
