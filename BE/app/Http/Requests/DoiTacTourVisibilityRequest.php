<?php

namespace App\Http\Requests;

class DoiTacTourVisibilityRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'trang_thai_hien_thi' => 'required|boolean',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Cập nhật hiển thị tour thất bại';
    }
}

