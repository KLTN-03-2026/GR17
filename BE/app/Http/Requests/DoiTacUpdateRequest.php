<?php

namespace App\Http\Requests;

class DoiTacUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        $maDoiTac = $this->route('ma_doi_tac');

        return [
            'ten_doi_tac' => 'sometimes|string|max:255',
            'ten_nguoi_dai_dien' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:doi_tac,email,' . $maDoiTac . ',ma_doi_tac',
            'mat_khau' => 'sometimes|string|min:6',
            'so_dien_thoai' => 'sometimes|nullable|string|max:20',
            'dia_chi' => 'sometimes|nullable|string|max:255',
            'is_block' => 'sometimes|boolean',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Cập nhật đối tác thất bại';
    }
}

