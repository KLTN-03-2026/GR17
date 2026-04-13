<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AIPlannerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow all users
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'diem_den' => 'required|string',
            'so_ngay' => 'required|integer|min:1|max:7',
            'ngan_sach' => 'required|numeric',
        ];
    }

    /**
     * Return custom validation messages.
     */
    public function messages(): array
    {
        return [
            'diem_den.required' => 'Vui lòng nhập điểm đến cho chuyến đi.',
            'diem_den.string' => 'Điểm đến không hợp lệ.',
            'so_ngay.required' => 'Vui lòng nhập số ngày.',
            'so_ngay.integer' => 'Số ngày phải là một số nguyên.',
            'so_ngay.min' => 'Thời gian đi tối thiểu là 1 ngày.',
            'so_ngay.max' => 'Hệ thống AI hiện giới hạn lên lịch trình tối đa 7 ngày.',
            'ngan_sach.required' => 'Vui lòng nhập ngân sách (có thể nhập 0 nếu đi tự túc hoàn toàn).',
            'ngan_sach.numeric' => 'Ngân sách phải là một số hợp lệ.',
        ];
    }

    /**
     * Customize the response on validation failure.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
            'code' => 'VALIDATION_ERROR',
            'errors' => $validator->errors()
        ], 422));
    }
}
