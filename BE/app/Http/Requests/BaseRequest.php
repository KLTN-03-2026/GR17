<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        $message = 'Dữ liệu không hợp lệ';

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $this->getErrorMessage() ?? $message,
            'errors' => $validator->errors(),
        ], 422));
    }

    protected function getErrorMessage(): ?string
    {
        return null;
    }
}
