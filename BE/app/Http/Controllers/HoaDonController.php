<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHoaDonCustomerRequest;
use App\Http\Requests\UpdateHoaDonStatusRequest;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\ThanhVienNhom;
use App\Services\CustomerInvoicePresenter;
use App\Services\CustomerTourCheckoutService;
use App\Services\HoaDonPaymentSyncService;
use App\Services\SepayTransactionSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HoaDonController extends Controller
{
    public function __construct(
        private readonly CustomerInvoicePresenter $invoicePresenter,
        private readonly CustomerTourCheckoutService $checkoutService,
        private readonly HoaDonPaymentSyncService $paymentSyncService,
        private readonly SepayTransactionSyncService $sepayTransactionSyncService,
    ) {
    }

    public function indexAdmin(): JsonResponse
    {
        $hoaDons = HoaDon::with(['nhom', 'tour', 'tourKhoiHanh', 'khachHangDat', 'latestQrPayment'])
            ->orderByDesc('ngay_tao')
            ->get()
            ->map(fn (HoaDon $hoaDon) => $this->invoicePresenter->present($hoaDon))
            ->values();

        if ($hoaDons->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có hóa đơn nào',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách hóa đơn thành công',
            'data' => $hoaDons,
        ]);
    }

    public function showAdmin(string $ma_hoa_don): JsonResponse
    {
        $hoaDon = HoaDon::with(['nhom', 'tour', 'tourKhoiHanh', 'khachHangDat', 'latestQrPayment'])
            ->find($ma_hoa_don);

        if (!$hoaDon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hóa đơn',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy hóa đơn thành công',
            'data' => $this->invoicePresenter->present($hoaDon),
        ]);
    }

    public function updateStatusAdmin(UpdateHoaDonStatusRequest $request, string $ma_hoa_don): JsonResponse
    {
        $hoaDon = HoaDon::with(['tour', 'tourKhoiHanh', 'latestQrPayment'])->find($ma_hoa_don);
        if (!$hoaDon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hóa đơn',
            ], 404);
        }

        if ($hoaDon->payment_method === 'vietqr_bank_transfer') {
            return response()->json([
                'success' => false,
                'message' => 'Hóa đơn QR được đồng bộ trạng thái từ webhook hoặc luồng tạo lại QR, không được sửa tay.',
            ], 422);
        }

        try {
            $newStatus = (int) $request->validated()['trang_thai_thanh_toan'];
            $hoaDon = $this->paymentSyncService->applyLegacyStatus($hoaDon, $newStatus);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thanh toán thành công',
                'data' => $this->invoicePresenter->present($hoaDon),
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi khi cập nhật hóa đơn',
            ], 500);
        }
    }

    public function indexCustomer(Request $request): JsonResponse
    {
        $khachHang = $request->user();
        if (!$khachHang instanceof KhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn phải đăng nhập để xem lịch sử đơn hàng.',
            ], 401);
        }

        $hoaDons = $this->customerInvoiceQuery($khachHang->Ma_khach_hang)
            ->get()
            ->map(fn (HoaDon $hoaDon) => $this->invoicePresenter->present($hoaDon))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách hóa đơn thành công',
            'data' => $hoaDons,
        ]);
    }

    public function showCustomer(Request $request, string $ma_hoa_don): JsonResponse
    {
        $khachHang = $request->user();
        if (!$khachHang instanceof KhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn phải đăng nhập để xem hóa đơn.',
            ], 401);
        }

        $hoaDon = $this->customerInvoiceQuery($khachHang->Ma_khach_hang)
            ->where('ma_hoa_don', $ma_hoa_don)
            ->first();

        if (!$hoaDon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hóa đơn thuộc tài khoản của bạn.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin hóa đơn thành công',
            'data' => $this->invoicePresenter->present($hoaDon),
        ]);
    }

    public function paymentStatus(Request $request, string $ma_hoa_don): JsonResponse
    {
        $khachHang = $request->user();
        if (!$khachHang instanceof KhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn phải đăng nhập để xem hóa đơn.',
            ], 401);
        }

        $hoaDon = $this->customerInvoiceQuery($khachHang->Ma_khach_hang)
            ->where('ma_hoa_don', $ma_hoa_don)
            ->first();

        if (!$hoaDon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hóa đơn thuộc tài khoản của bạn.',
            ], 404);
        }

        $hoaDon = $this->sepayTransactionSyncService->syncInvoicePaymentStatus($hoaDon);

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin hóa đơn thành công',
            'data' => $this->invoicePresenter->present($hoaDon),
        ]);
    }

    public function retryPayment(Request $request, string $ma_hoa_don): JsonResponse
    {
        $khachHang = $request->user();
        if (!$khachHang instanceof KhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn phải đăng nhập để tạo lại mã QR.',
            ], 401);
        }

        $hoaDon = $this->customerInvoiceQuery($khachHang->Ma_khach_hang)
            ->where('ma_hoa_don', $ma_hoa_don)
            ->first();

        if (!$hoaDon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hóa đơn thuộc tài khoản của bạn.',
            ], 404);
        }

        try {
            $invoice = $this->checkoutService->retryCheckout($hoaDon);

            return response()->json([
                'success' => true,
                'message' => 'Đã tạo lại mã QR thanh toán.',
                'data' => $invoice,
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => collect($exception->errors())->flatten()->first() ?: 'Không thể tạo lại mã QR.',
                'errors' => $exception->errors(),
            ], 422);
        }
    }

    public function cancelPayment(Request $request, string $ma_hoa_don): JsonResponse
    {
        $khachHang = $request->user();
        if (!$khachHang instanceof KhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn phải đăng nhập để hủy thanh toán.',
            ], 401);
        }

        $hoaDon = $this->customerInvoiceQuery($khachHang->Ma_khach_hang)
            ->where('ma_hoa_don', $ma_hoa_don)
            ->first();

        if (!$hoaDon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hóa đơn thuộc tài khoản của bạn.',
            ], 404);
        }

        if ($hoaDon->payment_method !== 'vietqr_bank_transfer') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ hỗ trợ hủy thanh toán cho hóa đơn QR ngân hàng.',
            ], 422);
        }

        $hoaDon->loadMissing(['latestQrPayment', 'latestPendingQrPayment']);
        $paymentStatus = $this->invoicePresenter->resolvePaymentStatus($hoaDon);

        if ($paymentStatus !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ hóa đơn đang chờ thanh toán mới có thể hủy.',
            ], 422);
        }

        $invoice = $this->paymentSyncService->markAsFailed(
            $hoaDon,
            'customer_cancelled',
            $hoaDon->latestPendingQrPayment ?: $hoaDon->latestQrPayment
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy thanh toán hóa đơn.',
            'data' => $this->invoicePresenter->present($invoice),
        ]);
    }

    public function storeCustomer(StoreHoaDonCustomerRequest $request): JsonResponse
    {
        $khachHang = $request->user();
        $maKhachHang = $request->ma_khach_hang ?? ($khachHang instanceof KhachHang ? $khachHang->Ma_khach_hang : null);

        if (!$maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn phải xác thực khách hàng để thêm hóa đơn.',
            ], 403);
        }

        $isMember = ThanhVienNhom::where('Ma_khach_hang', $maKhachHang)
            ->where('Ma_nhom', $request->ma_nhom)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không nằm trong nhóm này để tạo hóa đơn.',
            ], 403);
        }

        try {
            $data = $request->validated();
            $data['trang_thai_thanh_toan'] = 0;
            $data['payment_method'] = $data['payment_method'] ?? 'legacy_manual';
            $data['payment_status'] = $data['payment_status'] ?? 'pending';
            $data['ma_khach_hang_dat'] = $data['ma_khach_hang_dat'] ?? $maKhachHang;
            $data['ngay_tao'] = now();

            $hoaDon = HoaDon::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Thêm hóa đơn thành công',
                'data' => $hoaDon,
            ], 201);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi thêm hóa đơn.',
            ], 500);
        }
    }

    private function customerInvoiceQuery(string $maKhachHang)
    {
        $maNhoms = ThanhVienNhom::query()
            ->where('Ma_khach_hang', $maKhachHang)
            ->pluck('Ma_nhom');

        return HoaDon::query()
            ->with(['tour', 'tourKhoiHanh', 'khachHangDat', 'latestQrPayment'])
            ->where(function ($query) use ($maKhachHang, $maNhoms): void {
                $query->where('ma_khach_hang_dat', $maKhachHang);

                if ($maNhoms->isNotEmpty()) {
                    $query->orWhereIn('ma_nhom', $maNhoms);
                }
            })
            ->orderByDesc('ngay_tao');
    }
}
