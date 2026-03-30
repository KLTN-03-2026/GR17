<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
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
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $message = "Dữ liệu không hợp lệ";
        
        // Custom message based on controller context if necessary, 
        // but for now, generic or provided by child.
        
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $this->getErrorMessage() ?? $message,
            'errors'  => $validator->errors()
        ], 422));
    }

    protected function getErrorMessage()
    {
        return null;
    }
}
