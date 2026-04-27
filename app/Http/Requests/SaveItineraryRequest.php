<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SaveItineraryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'ma_khach_hang' => $this->input('ma_khach_hang', $this->input('maKhachHang')),
            'ket_qua_ai' => $this->input('ket_qua_ai', $this->input('ketQuaAi')),
            'thong_tin_chuyen_di' => $this->input('thong_tin_chuyen_di', $this->input('thongTinChuyenDi')),
        ]);
    }

    public function rules(): array
    {
        return [
            'ma_khach_hang' => 'required',
            'ket_qua_ai' => 'required|array',
            'thong_tin_chuyen_di' => 'required|array',
        ];
    }

    public function messages(): array
    {
        return [
            'ma_khach_hang.required' => 'Thông tin khách hàng là bắt buộc.',
            'ket_qua_ai.required' => 'Dữ liệu lịch trình AI là bắt buộc.',
            'ket_qua_ai.array' => 'Dữ liệu lịch trình AI không hợp lệ.',
            'thong_tin_chuyen_di.required' => 'Thông tin chuyến đi là bắt buộc.',
            'thong_tin_chuyen_di.array' => 'Thông tin chuyến đi không hợp lệ.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
            'code' => 'VALIDATION_ERROR',
            'errors' => $validator->errors(),
        ], 422));
    }
}
