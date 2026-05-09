<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AIPlannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'diem_den' => $this->input('diem_den', $this->input('diemDen')),
            'so_ngay' => $this->input('so_ngay', $this->input('soNgay')),
            'ngay_bat_dau' => $this->input('ngay_bat_dau', $this->input('ngayBatDau')),
            'ngay_ket_thuc' => $this->input('ngay_ket_thuc', $this->input('ngayKetThuc')),
            'ngan_sach' => $this->input('ngan_sach', $this->input('nganSach')),
            'so_thich' => $this->input('so_thich', $this->input('soThich', [])),
            'mo_ta_chuyen_di' => $this->input('mo_ta_chuyen_di', $this->input('moTaChuyenDi', $this->input('moTa', ''))),
        ]);
    }

    public function rules(): array
    {
        return [
            'diem_den' => 'required|string',
            'so_ngay' => 'nullable|integer|min:1|max:7',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'ngan_sach' => 'required',
        ];
    }

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
