<?php

namespace App\Http\Requests;

class StoreTagRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ma_tag' => 'required|string|unique:tag|max:10',
            'ten_tag' => 'required|string|min:2|max:100',
        ];
    }

    protected function getErrorMessage()
    {
        return 'Thêm tag thất bại';
    }
}
