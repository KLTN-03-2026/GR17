<?php

namespace App\Http\Requests;

class UpdateTagRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ten_tag' => 'required|string|min:2|max:100',
        ];
    }

    protected function getErrorMessage(): ?string
    {
        return 'Cập nhật tag thất bại';
    }
}
