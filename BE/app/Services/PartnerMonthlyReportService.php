<?php

namespace App\Services;

use App\Models\DoiSoatHoaHong;
use Carbon\Carbon;

class PartnerMonthlyReportService
{
    public function buildForMonth(string $maDoiTac, string $month): array
    {
        $start = Carbon::createFromFormat('Y-m', $month, 'Asia/Ho_Chi_Minh')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $rows = DoiSoatHoaHong::query()
            ->where('ma_doi_tac', $maDoiTac)
            ->whereBetween('created_at', [$start, $end])
            ->get([
                'tong_tien_giao_dich',
                'tien_hoa_hong_admin',
                'tien_doi_tac_thuc_nhan',
                'trang_thai_thanh_toan',
            ]);

        $grossRevenue = (float) $rows->sum('tong_tien_giao_dich');
        $platformCommission = (float) $rows->sum('tien_hoa_hong_admin');
        $partnerRevenue = (float) $rows->sum('tien_doi_tac_thuc_nhan');
        $paidToPartner = (float) $rows
            ->where('trang_thai_thanh_toan', 'da_chuyen_khoan')
            ->sum('tien_doi_tac_thuc_nhan');
        $pendingToPartner = (float) $rows
            ->where('trang_thai_thanh_toan', 'chua_doi_soat')
            ->sum('tien_doi_tac_thuc_nhan');

        return [
            'month' => $month,
            'period' => [
                'from' => $start->toDateString(),
                'to' => $end->toDateString(),
            ],
            'metrics' => [
                'total_orders' => $rows->count(),
                'gross_revenue' => $grossRevenue,
                'platform_commission' => $platformCommission,
                'partner_revenue' => $partnerRevenue,
                'paid_to_partner' => $paidToPartner,
                'pending_to_partner' => $pendingToPartner,
            ],
        ];
    }
}
