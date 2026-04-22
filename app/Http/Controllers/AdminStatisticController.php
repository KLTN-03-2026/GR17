<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use App\Models\DoiSoatHoaHong;
use App\Models\KhachHang;
use App\Models\DoiTac;
use App\Models\Tour;
use App\Models\DiaDiem;
use App\Models\KeHoach;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminStatisticController extends Controller
{
    /**
     * Thống kê Tài chính nền tảng
     */
    public function getPlatformRevenue(Request $request)
    {
        $period = $request->query('period', 'month');

        // Query filter
        $queryBase = HoaDon::query();
        if ($fromDate = $request->query('from_date')) {
            $queryBase->whereDate('ngay_tao', '>=', $fromDate);
        }
        if ($toDate = $request->query('to_date')) {
            $queryBase->whereDate('ngay_tao', '<=', $toDate);
        }

        // Tổng doanh thu 
        $totalRevenueQuery = clone $queryBase;
        $totalRevenueQuery->where(function($q) {
            $q->where('trang_thai_thanh_toan', 1)->orWhere('payment_status', 'paid');
        });
        $totalRevenue = $totalRevenueQuery->sum('tong_tien');

        // Thống kê hóa đơn (số lượng)
        $totalInvoices = (clone $queryBase)->count();
        $successInvoices = (clone $queryBase)->where(function($q) {
            $q->where('trang_thai_thanh_toan', 1)->orWhere('payment_status', 'paid');
        })->count();
        $pendingInvoices = (clone $queryBase)->where(function($q) {
            $q->where('trang_thai_thanh_toan', 0)->where(function($q2) {
                $q2->whereNull('payment_status')->orWhere('payment_status', 'pending');
            });
        })->count();
        $canceledInvoices = $totalInvoices - $successInvoices - $pendingInvoices;

        // Tiền hoa hồng
        $commissionQueryBase = DoiSoatHoaHong::query();
        if ($fromDate) {
            $commissionQueryBase->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $commissionQueryBase->whereDate('created_at', '<=', $toDate);
        }
        $totalCommission = (clone $commissionQueryBase)->sum('tien_hoa_hong_admin');
        
        $chartDataCommission = (clone $commissionQueryBase)->select(
            DB::raw('SUM(tien_hoa_hong_admin) as total'),
            DB::raw($this->getGroupByExpression($period, 'created_at') . ' as label')
        )
        ->groupBy('label')
        ->orderBy('label', 'asc')
        ->get();

        // Nhóm doanh thu
        $chartData = (clone $queryBase)->where(function($q) {
                $q->where('trang_thai_thanh_toan', 1)->orWhere('payment_status', 'paid');
            })->select(
                DB::raw('SUM(tong_tien) as total'),
                DB::raw($this->getGroupByExpression($period, 'ngay_tao') . ' as label')
            )
            ->groupBy('label')
            ->orderBy('label', 'asc')
            ->get();

        // Tổng người dùng
        $totalKhachHang = KhachHang::count();
        $totalTours = Tour::count();
        $totalKeHoach = KeHoach::whereNotNull('du_lieu_ai')->count();

        // Top 5 Tours bán chạy nhất
        $topToursQuery = HoaDon::where('loai_hoa_don', 2)
            ->where(function($q) {
                $q->where('trang_thai_thanh_toan', 1)->orWhere('payment_status', 'paid');
            });
            
        if ($fromDate) {
            $topToursQuery->whereDate('ngay_tao', '>=', $fromDate);
        }
        if ($toDate) {
            $topToursQuery->whereDate('ngay_tao', '<=', $toDate);
        }

        $topTours = $topToursQuery->select('ma_doi_tuong', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(tong_tien) as revenue'))
            ->groupBy('ma_doi_tuong')
            ->orderBy('total_orders', 'desc')
            ->limit(5)
            ->with(['tour' => function ($q) {
                $q->select('ma_tour', 'ten_tour', 'hinh_anh', 'ma_doi_tac')->with(['doiTac' => function($q2) {
                    $q2->select('ma_doi_tac', 'ten_doi_tac');
                }]);
            }])
            ->get();

        return response()->json([
            'success' => true,
            'tong_quan' => [
                'tong_doanh_thu' => $totalRevenue,
                'tong_hoa_hong' => $totalCommission,
                'so_luong_hoa_don' => [
                    'tong' => $totalInvoices,
                    'thanh_cong' => $successInvoices,
                    'cho_xu_ly' => $pendingInvoices,
                    'huy' => $canceledInvoices,
                ],
                'tong_khach_hang' => $totalKhachHang,
                'tong_tour' => $totalTours,
                'tong_ke_hoach' => $totalKeHoach
            ],
            'bieu_do' => $chartData,
            'bieu_do_hoa_hong' => $chartDataCommission,
            'top_tours' => $topTours
        ], 200);
    }

    /**
     * Thống kê Người dùng
     */
    public function getUserStatistics(Request $request)
    {
        $totalKhachHang = KhachHang::count();
        $totalDoiTac = DoiTac::count();

        // New customers in current month
        $customersThisMonth = KhachHang::whereMonth('created_at', Carbon::now()->month)
                                     ->whereYear('created_at', Carbon::now()->year)->count();
        $partnersThisMonth = DoiTac::whereMonth('created_at', Carbon::now()->month)
                                   ->whereYear('created_at', Carbon::now()->year)->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_khach_hang' => $totalKhachHang,
                'total_doi_tac' => $totalDoiTac,
                'new_customers_this_month' => $customersThisMonth,
                'new_partners_this_month' => $partnersThisMonth,
            ]
        ], 200);
    }

    /**
     * Thống kê Địa điểm & Tour
     */
    public function getLocationAndTourStatistics(Request $request)
    {
        $totalTours = Tour::count();
        $totalDiaDiem = DiaDiem::count();
        $totalKeHoachAI = KeHoach::whereNotNull('ma_khach_hang')->count();

        // Top Tours mua nhiều nhất
        $topTours = HoaDon::where('loai_hoa_don', 2) 
            ->select('ma_doi_tuong', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(tong_tien) as revenue'))
            ->groupBy('ma_doi_tuong')
            ->orderBy('total_orders', 'desc')
            ->limit(5)
            ->with(['tour' => function ($q) {
                $q->select('ma_tour', 'ten_tour', 'hinh_anh');
            }])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_tours' => $totalTours,
                'total_dia_diem' => $totalDiaDiem,
                'total_ke_hoach_ai' => $totalKeHoachAI,
                'top_tours' => $topTours
            ]
        ], 200);
    }

    /**
     * Thống kê Đối soát 
     */
    public function getReconciliationStatistics(Request $request)
    {
        $totalDoiSoat = DoiSoatHoaHong::sum('tong_tien_giao_dich');
        $paidToPartners = DoiSoatHoaHong::where('trang_thai_thanh_toan', 'paid')->sum('tien_doi_tac_thuc_nhan');
        $pendingToPartners = DoiSoatHoaHong::where('trang_thai_thanh_toan', 'pending')->sum('tien_doi_tac_thuc_nhan');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_transaction_value' => $totalDoiSoat,
                'paid_to_partners' => $paidToPartners,
                'pending_to_partners' => $pendingToPartners
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
