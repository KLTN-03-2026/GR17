<?php

namespace App\Services;

use App\Models\GiaoDichQr;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\Nhom;
use App\Models\ThanhVienNhom;
use App\Models\Tour;
use App\Models\TourKhoiHanh;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CustomerTourCheckoutService
{
    public function __construct(
        private readonly InvoiceCodeGenerator $invoiceCodeGenerator,
        private readonly VietQrService $vietQrService,
        private readonly CustomerInvoicePresenter $invoicePresenter,
    ) {
    }

    public function createCheckout(
        KhachHang $khachHang,
        string $maTour,
        string $maThoiGianTour,
        array $thongTinNguoiDat,
    ): array {
        $tour = Tour::publiclyVisible()
            ->where('ma_tour', $maTour)
            ->first();

        if (!$tour) {
            throw ValidationException::withMessages([
                'ma_tour' => 'Tour không tồn tại hoặc chưa sẵn sàng để đặt.',
            ]);
        }

        $lichKhoiHanh = TourKhoiHanh::query()
            ->where('ma_tour', $maTour)
            ->where('ma_thoi_gian_tour', $maThoiGianTour)
            ->first();

        if (!$lichKhoiHanh) {
            throw ValidationException::withMessages([
                'ma_thoi_gian_tour' => 'Không tìm thấy lịch khởi hành phù hợp.',
            ]);
        }

        if (!$lichKhoiHanh->tinh_trang || (int) $lichKhoiHanh->so_cho < 1) {
            throw ValidationException::withMessages([
                'ma_thoi_gian_tour' => 'Lịch khởi hành này đã đóng hoặc hết chỗ.',
            ]);
        }

        $tongTien = (int) round((float) $tour->so_tien);
        if ($tongTien <= 0) {
            throw ValidationException::withMessages([
                'ma_tour' => 'Tour này chưa có giá hợp lệ để thanh toán.',
            ]);
        }

        return DB::transaction(function () use ($khachHang, $tour, $lichKhoiHanh, $thongTinNguoiDat, $tongTien): array {
            $maHoaDon = $this->invoiceCodeGenerator->generate();
            $maNhom = 'N' . $maHoaDon;
            $maThanhVien = 'TV' . $maHoaDon;

            Nhom::create([
                'Ma_nhom' => $maNhom,
                'ten_nhom' => "Đơn tour {$maHoaDon}",
            ]);

            ThanhVienNhom::create([
                'Ma_thanh_vien' => $maThanhVien,
                'Ma_nhom' => $maNhom,
                'Ma_khach_hang' => $khachHang->Ma_khach_hang,
                'vai_tro' => 1,
            ]);

            $qrData = $this->vietQrService->createPaymentQr($maHoaDon, $tongTien);

            $hoaDon = HoaDon::create([
                'ma_hoa_don' => $maHoaDon,
                'ma_nhom' => $maNhom,
                'ma_khach_hang_dat' => $khachHang->Ma_khach_hang,
                'loai_hoa_don' => 0,
                'ma_doi_tuong' => $tour->ma_tour,
                'ma_thoi_gian_tour' => $lichKhoiHanh->ma_thoi_gian_tour,
                'tong_tien' => $tongTien,
                'trang_thai_thanh_toan' => 0,
                'payment_method' => 'vietqr_bank_transfer',
                'payment_status' => 'pending',
                'ten_nguoi_dat' => $thongTinNguoiDat['ho_ten'] ?? $khachHang->Ho_va_ten,
                'email_nguoi_dat' => $thongTinNguoiDat['email'] ?? $khachHang->Email,
                'so_dien_thoai_nguoi_dat' => $thongTinNguoiDat['so_dien_thoai'] ?? $khachHang->so_dien_thoai,
                'dia_chi_nguoi_dat' => $thongTinNguoiDat['dia_chi'] ?? null,
                'ngay_tao' => now(),
            ]);

            $this->createQrAttempt($hoaDon, $tongTien, $qrData);

            return $this->invoicePresenter->present(
                $hoaDon->fresh(['tour', 'tourKhoiHanh', 'khachHangDat', 'latestQrPayment'])
            );
        });
    }

    public function retryCheckout(HoaDon $hoaDon): array
    {
        $hoaDon->loadMissing(['tour', 'tourKhoiHanh', 'khachHangDat', 'latestQrPayment']);

        if ((int) $hoaDon->loai_hoa_don !== 0) {
            throw ValidationException::withMessages([
                'ma_hoa_don' => 'Chỉ hóa đơn đặt tour mới được tạo lại QR thanh toán.',
            ]);
        }

        $paymentStatus = $this->invoicePresenter->resolvePaymentStatus($hoaDon);
        if (!in_array($paymentStatus, ['expired', 'failed'], true)) {
            throw ValidationException::withMessages([
                'ma_hoa_don' => 'Chỉ hóa đơn đã hết hạn hoặc thanh toán thất bại mới được tạo lại QR.',
            ]);
        }

        $lichKhoiHanh = $hoaDon->tourKhoiHanh;
        if (!$lichKhoiHanh || !$lichKhoiHanh->tinh_trang || (int) $lichKhoiHanh->so_cho < 1) {
            throw ValidationException::withMessages([
                'ma_thoi_gian_tour' => 'Lịch khởi hành hiện tại không còn hợp lệ để thanh toán lại.',
            ]);
        }

        $tongTien = (int) round((float) $hoaDon->tong_tien);
        if ($tongTien <= 0) {
            throw ValidationException::withMessages([
                'ma_hoa_don' => 'Hóa đơn này không có số tiền hợp lệ để tạo lại QR.',
            ]);
        }

        return DB::transaction(function () use ($hoaDon, $tongTien): array {
            GiaoDichQr::query()
                ->where('ma_hoa_don', $hoaDon->ma_hoa_don)
                ->where('trang_thai', 'pending')
                ->update([
                    'trang_thai' => 'expired',
                ]);

            $hoaDon->forceFill([
                'payment_status' => 'pending',
                'trang_thai_thanh_toan' => 0,
                'payment_reference' => null,
                'ma_giao_dich' => null,
                'paid_at' => null,
            ])->save();

            $qrData = $this->vietQrService->createPaymentQr($hoaDon->ma_hoa_don, $tongTien);
            $this->createQrAttempt($hoaDon, $tongTien, $qrData);

            return $this->invoicePresenter->present(
                $hoaDon->fresh(['tour', 'tourKhoiHanh', 'khachHangDat', 'latestQrPayment'])
            );
        });
    }

    private function createQrAttempt(HoaDon $hoaDon, int $tongTien, array $qrData): GiaoDichQr
    {
        return GiaoDichQr::create([
            'ma_giao_dich_qr' => (string) Str::ulid(),
            'ma_hoa_don' => $hoaDon->ma_hoa_don,
            'provider' => 'vietqr',
            'so_tien' => $tongTien,
            'noi_dung_chuyen_khoan' => $hoaDon->ma_hoa_don,
            'qr_url' => $qrData['qr_url'],
            'qr_payload' => $qrData['qr_payload'] ?? null,
            'trang_thai' => 'pending',
            'expires_at' => now()->addMinutes((int) config('payment.qr_expire_minutes', 5)),
        ]);
    }
}
