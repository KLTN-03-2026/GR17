<?php

namespace App\Http\Controllers;

use App\Models\DoiSoatHoaHong;
use Illuminate\Http\Request;

class DoiTacDoiSoatController extends Controller
{
    public function index(Request $request)
    {
        $doiTac = $request->user();
        
        if (!$doiTac || !isset($doiTac->ma_doi_tac)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập',
            ], 403);
        }

        $maDoiTac = $doiTac->ma_doi_tac;

        // Fetch all reconciliations for this partner
        $doiSoatList = DoiSoatHoaHong::with(['hoaDon'])
            ->where('ma_doi_tac', $maDoiTac)
            ->orderByDesc('created_at')
            ->get();

        // Calculate statistics for this partner
        $tongDoanhThuThem = $doiSoatList->sum('tong_tien_giao_dich');
        $tongHoaHongPhaiTra = $doiSoatList->sum('tien_hoa_hong_admin');
        
        // Tiền thực nhận của đối tác
        $tongTienDaNhan = $doiSoatList->where('trang_thai_thanh_toan', 'da_chuyen_khoan')->sum('tien_doi_tac_thuc_nhan');
        $tongTienDangCho = $doiSoatList->where('trang_thai_thanh_toan', 'chua_doi_soat')->sum('tien_doi_tac_thuc_nhan');

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin tài chính đối tác thành công',
            'data' => [
                'thong_ke' => [
                    'tong_doanh_thu_ban_duoc' => $tongDoanhThuThem,
                    'phi_hoa_hong_nen_tang' => $tongHoaHongPhaiTra, // 10%
                    'tong_tien_da_nhan' => $tongTienDaNhan,
                    'tong_tien_dang_cho' => $tongTienDangCho,
                ],
                'danh_sach' => $doiSoatList,
            ]
        ], 200);
    }
}
