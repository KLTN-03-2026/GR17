<?php

namespace App\Services;

use App\Models\HoaDon;

class CustomerInvoicePresenter
{
    public function present(HoaDon $hoaDon): array
    {
        $hoaDon->loadMissing([
            'tour',
            'tourKhoiHanh',
            'khachHangDat',
            'latestQrPayment',
        ]);

        $qrPayment = $hoaDon->latestQrPayment;
        $paymentStatus = $this->resolvePaymentStatus($hoaDon);
        $customerSummary = $this->buildCustomerSummary($hoaDon);
        $latestQrSummary = $this->buildLatestQrPaymentSummary($qrPayment, $paymentStatus);

        return [
            'ma_hoa_don' => $hoaDon->ma_hoa_don,
            'loai_hoa_don' => (int) $hoaDon->loai_hoa_don,
            'ma_doi_tuong' => $hoaDon->ma_doi_tuong,
            'ma_tour' => $hoaDon->loai_hoa_don === 0 ? $hoaDon->ma_doi_tuong : null,
            'ten_tour' => $hoaDon->tour?->ten_tour,
            'ma_thoi_gian_tour' => $hoaDon->ma_thoi_gian_tour,
            'ma_nhom' => $hoaDon->ma_nhom,
            'ma_khach_hang_dat' => $hoaDon->ma_khach_hang_dat,
            'ten_nguoi_dat' => $customerSummary['ho_ten'],
            'email_nguoi_dat' => $customerSummary['email'],
            'so_dien_thoai_nguoi_dat' => $customerSummary['so_dien_thoai'],
            'dia_chi_nguoi_dat' => $hoaDon->dia_chi_nguoi_dat,
            'customer_summary' => $customerSummary,
            'nhom_summary' => [
                'ma_nhom' => $hoaDon->ma_nhom,
                'ten_nhom' => $hoaDon->nhom?->Ten_nhom ?: $hoaDon->nhom?->ten_nhom,
            ],
            'so_tien' => (float) $hoaDon->tong_tien,
            'tong_tien' => (float) $hoaDon->tong_tien,
            'payment_method' => $hoaDon->payment_method,
            'payment_status' => $paymentStatus,
            'payment_reference' => $hoaDon->payment_reference,
            'ma_giao_dich' => $hoaDon->ma_giao_dich,
            'trang_thai_thanh_toan' => (int) $hoaDon->trang_thai_thanh_toan,
            'noi_dung_chuyen_khoan' => $qrPayment?->noi_dung_chuyen_khoan ?? $hoaDon->ma_hoa_don,
            'qr_url' => $qrPayment?->qr_url,
            'payment_expires_at' => $qrPayment?->expires_at?->toISOString(),
            'latest_qr_payment_summary' => $latestQrSummary,
            'created_at' => ($hoaDon->ngay_tao ?? $hoaDon->created_at)?->toISOString(),
            'ngay_tao' => ($hoaDon->ngay_tao ?? $hoaDon->created_at)?->toISOString(),
            'paid_at' => $hoaDon->paid_at?->toISOString() ?? $qrPayment?->paid_at?->toISOString(),
        ];
    }

    public function presentPartner(HoaDon $hoaDon): array
    {
        $presented = $this->present($hoaDon);

        return [
            'ma_hoa_don' => $presented['ma_hoa_don'],
            'ma_tour' => $presented['ma_tour'],
            'ten_tour' => $presented['ten_tour'],
            'ma_thoi_gian_tour' => $presented['ma_thoi_gian_tour'],
            'ma_nhom' => $presented['ma_nhom'],
            'so_tien' => $presented['so_tien'],
            'ngay_tao' => $presented['ngay_tao'],
            'payment_method' => $presented['payment_method'],
            'payment_status' => $presented['payment_status'],
            'payment_reference' => $presented['payment_reference'],
            'ma_giao_dich' => $presented['ma_giao_dich'],
            'paid_at' => $presented['paid_at'],
            'customer_summary' => $presented['customer_summary'],
            'latest_qr_payment_summary' => $presented['latest_qr_payment_summary'],
            'trang_thai_thanh_toan' => $presented['trang_thai_thanh_toan'],
        ];
    }

    public function resolvePaymentStatus(HoaDon $hoaDon): string
    {
        $status = (string) ($hoaDon->payment_status ?: $this->mapLegacyStatus((int) $hoaDon->trang_thai_thanh_toan));
        $qrPayment = $hoaDon->latestQrPayment;

        if (
            $status === 'pending'
            && $qrPayment?->expires_at
            && $qrPayment->expires_at->isPast()
        ) {
            return 'expired';
        }

        return $status;
    }

    public function mapLegacyStatus(int $status): string
    {
        return match ($status) {
            1 => 'paid',
            2 => 'failed',
            default => 'pending',
        };
    }

    private function buildCustomerSummary(HoaDon $hoaDon): array
    {
        return [
            'ma_khach_hang' => $hoaDon->ma_khach_hang_dat,
            'ho_ten' => $hoaDon->ten_nguoi_dat ?: $hoaDon->khachHangDat?->Ho_va_ten,
            'email' => $hoaDon->email_nguoi_dat ?: $hoaDon->khachHangDat?->Email,
            'so_dien_thoai' => $hoaDon->so_dien_thoai_nguoi_dat ?: $hoaDon->khachHangDat?->so_dien_thoai,
        ];
    }

    private function buildLatestQrPaymentSummary($qrPayment, string $paymentStatus): ?array
    {
        if (!$qrPayment) {
            return null;
        }

        return [
            'ma_giao_dich_qr' => $qrPayment->ma_giao_dich_qr,
            'provider' => $qrPayment->provider,
            'trang_thai' => $paymentStatus === 'expired' ? 'expired' : $qrPayment->trang_thai,
            'so_tien' => (float) $qrPayment->so_tien,
            'noi_dung_chuyen_khoan' => $qrPayment->noi_dung_chuyen_khoan,
            'qr_url' => $qrPayment->qr_url,
            'reference_code' => $qrPayment->reference_code,
            'expires_at' => $qrPayment->expires_at?->toISOString(),
            'paid_at' => $qrPayment->paid_at?->toISOString(),
        ];
    }
}
