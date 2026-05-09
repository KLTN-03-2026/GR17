<?php

namespace App\Http\Requests;

class DoiTacDiaDiemRejectRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ly_do_tu_choi' => 'required|string|min:5|max:1000',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Từ chối địa điểm thất bại';
    }
}

