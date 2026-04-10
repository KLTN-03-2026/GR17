<?php

namespace App\Http\Requests;

class DoiTacStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_doi_tac' => 'nullable|string|max:10|unique:doi_tac,ma_doi_tac',
            'ten_doi_tac' => 'required|string|max:255',
            'ten_nguoi_dai_dien' => 'required|string|max:255',
            'email' => 'required|email|unique:doi_tac,email',
            'mat_khau' => 'required|string|min:6',
            'so_dien_thoai' => 'nullable|string|max:20',
            'dia_chi' => 'nullable|string|max:255',
            'is_block' => 'nullable|boolean',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Thêm đối tác thất bại';
    }
}

