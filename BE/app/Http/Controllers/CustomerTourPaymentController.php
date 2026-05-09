<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTourQrPaymentRequest;
use App\Models\KhachHang;
use App\Services\CustomerTourCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CustomerTourPaymentController extends Controller
{
    public function __construct(
        private readonly CustomerTourCheckoutService $checkoutService,
    ) {
    }

    public function store(CreateTourQrPaymentRequest $request, string $ma_tour): JsonResponse
    {
        $khachHang = $request->user();

        if (!$khachHang instanceof KhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn phải đăng nhập tài khoản khách hàng để thanh toán.',
            ], 403);
        }

        try {
            $data = $request->validated();
            $checkout = $this->checkoutService->createCheckout(
                $khachHang,
                $ma_tour,
                (string) $data['ma_thoi_gian_tour'],
                (array) $data['thong_tin_nguoi_dat'],
                $data['ma_voucher'] ?? null,
                (int) ($data['so_luong_khach'] ?? 1)
            );

            return response()->json([
                'success' => true,
                'message' => 'Tạo hóa đơn và mã QR thành công.',
                'data' => $checkout,
            ], 201);
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => collect($exception->errors())->flatten()->first() ?: 'Không thể tạo thanh toán QR.',
                'errors' => $exception->errors(),
            ], 422);
        }
    }
}
