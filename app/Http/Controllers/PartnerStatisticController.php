<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use App\Models\DoiSoatHoaHong;
use App\Models\Tour;
use App\Models\DiaDiem;
use App\Models\DanhGiaKeHoach;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PartnerStatisticController extends Controller
{
    /**
     * Thống kê Doanh thu Đối tác
     */
    public function getPartnerRevenue(Request $request)
    {
        $maDoiTac = $request->user()->ma_doi_tac;
        $period = $request->query('period', 'month');

        // Doanh thu từ HoaDon (các tour do đối tác tạo)
        $tours = Tour::where('ma_doi_tac', $maDoiTac)->pluck('ma_tour');

        // Note: Khách mua tour => loai_hoa_don = 2
        $query = HoaDon::whereIn('ma_doi_tuong', $tours)
            ->where(function($q) {
                $q->where('trang_thai_thanh_toan', 1)->orWhere('payment_status', 'paid');
            });

        $totalRevenue = $query->sum('tong_tien');

        // Doanh thu theo biểu đồ
        $chartData = HoaDon::whereIn('ma_doi_tuong', $tours)
            ->where(function($q) {
                $q->where('trang_thai_thanh_toan', 1)->orWhere('payment_status', 'paid');
            })
            ->select(
                DB::raw('SUM(tong_tien) as total_revenue'),
                DB::raw($this->getGroupByExpression($period, 'ngay_tao') . ' as date')
            )
            ->groupBy(DB::raw($this->getGroupByExpression($period, 'ngay_tao')))
            ->orderBy('date', 'asc')
            ->get();

        // Thông tin từ bảng DoiSoatHoaHong cho đối tác hiện tại
        $doiSoatData = DoiSoatHoaHong::where('ma_doi_tac', $maDoiTac)
            ->select(
                DB::raw('SUM(tien_hoa_hong_admin) as total_commission_paid_to_admin'),
                DB::raw('SUM(tien_doi_tac_thuc_nhan) as net_revenue'),
                DB::raw('SUM(CASE WHEN trang_thai_thanh_toan = "pending" THEN tien_doi_tac_thuc_nhan ELSE 0 END) as pending_payout')
            )
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_gross_revenue' => $totalRevenue,
                'net_revenue' => $doiSoatData->net_revenue ?? 0,
                'paid_to_admin_commission' => $doiSoatData->total_commission_paid_to_admin ?? 0,
                'pending_payout' => $doiSoatData->pending_payout ?? 0,
                'chart_data' => $chartData
            ]
        ], 200);
    }

    /**
     * Thống kê Đánh giá & Phản hồi
     */
    public function getFeedbackAndRatings(Request $request)
    {
        $maDoiTac = $request->user()->ma_doi_tac;
        
        // Lấy danh sách địa điểm của partner
        $diaDiems = DiaDiem::where('ma_doi_tac_tao', $maDoiTac)->pluck('ma_dia_diem');
        
        // Average rating & total ratings
        $avgRating = DanhGiaKeHoach::whereIn('ma_dia_diem', $diaDiems)->avg('so_sao');
        $totalRatings = DanhGiaKeHoach::whereIn('ma_dia_diem', $diaDiems)->count();

        // Phân bổ sao (1-5)
        $ratingDistribution = DanhGiaKeHoach::whereIn('ma_dia_diem', $diaDiems)
            ->select('so_sao', DB::raw('COUNT(*) as count'))
            ->groupBy('so_sao')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'average_rating' => round($avgRating ?? 0, 1),
                'total_ratings' => $totalRatings,
                'rating_distribution' => $ratingDistribution
            ]
        ], 200);
    }

    /**
     * Thống kê hiệu suất Tour
     */
    public function getTourPerformance(Request $request)
    {
        $maDoiTac = $request->user()->ma_doi_tac;

        $tours = Tour::where('ma_doi_tac', $maDoiTac)->pluck('ma_tour');

        $totalTours = $tours->count();
        $totalOrders = HoaDon::where('loai_hoa_don', 2)
                             ->whereIn('ma_doi_tuong', $tours)->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_tours' => $totalTours,
                'total_tour_orders' => $totalOrders,
            ]
        ], 200);
    }

    private function getGroupByExpression($period, $column)
    {
        if ($period == 'year') {
            return "YEAR(" . $column . ")";
        } elseif ($period == 'month') {
            return "DATE_FORMAT(" . $column . ", '%Y-%m')";
        } else {
            return "DATE(" . $column . ")";
        }
    }
}
