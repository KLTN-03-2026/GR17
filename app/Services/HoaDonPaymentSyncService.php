<?php

namespace App\Services;

use App\Models\DoiSoatHoaHong;
use App\Models\DoiTac;
use App\Models\GiaoDichQr;
use App\Models\HoaDon;
use App\Models\TourKhoiHanh;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class HoaDonPaymentSyncService
{
    public function applyLegacyStatus(
        HoaDon $hoaDon,
        int $newStatus,
        ?string $reference = null,
        ?CarbonInterface $paidAt = null,
        ?GiaoDichQr $qrPayment = null,
        array $webhookPayload = []
    ): HoaDon {
        return match ($newStatus) {
            1 => $this->markAsPaid($hoaDon, $reference, $paidAt, $qrPayment, $webhookPayload),
            2 => $this->markAsFailed($hoaDon, $reference, $qrPayment, $webhookPayload),
            default => $this->markAsPending($hoaDon, $qrPayment),
        };
    }

    public function markAsPaid(
        HoaDon $hoaDon,
        ?string $reference = null,
        ?CarbonInterface $paidAt = null,
        ?GiaoDichQr $qrPayment = null,
        array $webhookPayload = []
    ): HoaDon {
        $hoaDon->loadMissing(['tour', 'tourKhoiHanh', 'latestQrPayment']);

        if (($hoaDon->payment_status ?? null) === 'paid' || (int) $hoaDon->trang_thai_thanh_toan === 1) {
            return $hoaDon->fresh(['tour', 'tourKhoiHanh', 'latestQrPayment']);
        }

        return DB::transaction(function () use ($hoaDon, $reference, $paidAt, $qrPayment, $webhookPayload): HoaDon {
            $timestamp = $paidAt?->copy() ?? now();
            $targetQrPayment = $qrPayment ?: $hoaDon->latestPendingQrPayment ?: $hoaDon->latestQrPayment;

            $hoaDon->forceFill([
                'payment_status' => 'paid',
                'trang_thai_thanh_toan' => 1,
                'payment_reference' => $reference ?: $hoaDon->payment_reference,
                'ma_giao_dich' => $reference ?: $hoaDon->ma_giao_dich,
                'paid_at' => $timestamp,
            ])->save();

            if ($targetQrPayment) {
                $targetQrPayment->forceFill([
                    'trang_thai' => 'paid',
                    'reference_code' => $reference ?: $targetQrPayment->reference_code,
                    'paid_at' => $timestamp,
                    'webhook_payload' => !empty($webhookPayload) ? $webhookPayload : $targetQrPayment->webhook_payload,
                ])->save();
            }

            if ($hoaDon->ma_thoi_gian_tour) {
                TourKhoiHanh::query()
                    ->where('ma_thoi_gian_tour', $hoaDon->ma_thoi_gian_tour)
                    ->where('so_cho', '>=', $hoaDon->so_luong_khach ?? 1)
                    ->decrement('so_cho', $hoaDon->so_luong_khach ?? 1);
            }

            $this->createDoiSoatRecordIfNeeded($hoaDon->fresh(['tour']));

            return $hoaDon->fresh(['tour', 'tourKhoiHanh', 'latestQrPayment']);
        });
    }

    public function markAsFailed(
        HoaDon $hoaDon,
        ?string $reference = null,
        ?GiaoDichQr $qrPayment = null,
        array $webhookPayload = []
    ): HoaDon {
        $hoaDon->forceFill([
            'payment_status' => 'failed',
            'trang_thai_thanh_toan' => 2,
            'payment_reference' => $reference ?: $hoaDon->payment_reference,
            'paid_at' => null,
        ])->save();

        if ($qrPayment) {
            $qrPayment->forceFill([
                'trang_thai' => 'failed',
                'reference_code' => $reference ?: $qrPayment->reference_code,
                'webhook_payload' => !empty($webhookPayload) ? $webhookPayload : $qrPayment->webhook_payload,
                'paid_at' => null,
            ])->save();
        }

        return $hoaDon->fresh(['tour', 'tourKhoiHanh', 'latestQrPayment']);
    }

    public function markAsPending(HoaDon $hoaDon, ?GiaoDichQr $qrPayment = null): HoaDon
    {
        $hoaDon->forceFill([
            'payment_status' => 'pending',
            'trang_thai_thanh_toan' => 0,
            'paid_at' => null,
        ])->save();

        if ($qrPayment) {
            $qrPayment->forceFill([
                'trang_thai' => 'pending',
                'paid_at' => null,
            ])->save();
        }

        return $hoaDon->fresh(['tour', 'tourKhoiHanh', 'latestQrPayment']);
    }

    private function createDoiSoatRecordIfNeeded(HoaDon $hoaDon): void
    {
        if ((int) $hoaDon->loai_hoa_don !== 0) {
            return;
        }

        $tour = $hoaDon->tour;
        if (!$tour || !$tour->ma_doi_tac) {
            return;
        }

        $hasPartner = DoiTac::query()
            ->where('ma_doi_tac', $tour->ma_doi_tac)
            ->exists();

        if (!$hasPartner) {
            return;
        }

        $hasDoiSoat = DoiSoatHoaHong::query()
            ->where('ma_hoa_don', $hoaDon->ma_hoa_don)
            ->exists();

        if ($hasDoiSoat) {
            return;
        }

        $tongTien = (float) $hoaDon->tong_tien;
        $phanTramAdmin = 10.00;
        $tienAdmin = $tongTien * ($phanTramAdmin / 100);
        $tienDoiTac = $tongTien - $tienAdmin;

        DoiSoatHoaHong::create([
            'ma_hoa_don' => $hoaDon->ma_hoa_don,
            'ma_doi_tac' => $tour->ma_doi_tac,
            'loai_giao_dich' => 'tour',
            'tong_tien_giao_dich' => $tongTien,
            'phan_tram_hoa_hong' => $phanTramAdmin,
            'tien_hoa_hong_admin' => $tienAdmin,
            'tien_doi_tac_thuc_nhan' => $tienDoiTac,
            'trang_thai_thanh_toan' => 'chua_doi_soat',
            'mo_ta' => 'Thu tien 10% hoa hong tu viec ban Tour ' . $tour->ma_tour,
        ]);
    }
}
