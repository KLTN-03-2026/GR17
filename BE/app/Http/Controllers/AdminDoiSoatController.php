<?php

namespace App\Http\Controllers;

use App\Models\DoiSoatHoaHong;
use Illuminate\Http\Request;

class AdminDoiSoatController extends Controller
{
    public function index(Request $request)
    {
        $doiSoatList = DoiSoatHoaHong::with(['doiTac', 'hoaDon'])->orderByDesc('created_at')->get();

        $tongDoanhThu = $doiSoatList->sum('tong_tien_giao_dich');
        $tongHoaHongAdmin = $doiSoatList->sum('tien_hoa_hong_admin');
        $tongTienPhaiTraChuaDoiSoat = $doiSoatList->where('trang_thai_thanh_toan', 'chua_doi_soat')->sum('tien_doi_tac_thuc_nhan');

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách đối soát thành công',
            'data' => [
                'thong_ke' => [
                    'tong_doanh_thu' => $tongDoanhThu,
                    'tong_hoa_hong_admin' => $tongHoaHongAdmin,
                    'tong_cho_thanh_toan' => $tongTienPhaiTraChuaDoiSoat,
                ],
                'danh_sach' => $doiSoatList,
            ]
        ], 200);
    }

    public function markAsPaid(Request $request, string $ma_doi_soat)
    {
        $doiSoat = DoiSoatHoaHong::find($ma_doi_soat);
        if (!$doiSoat) {
             return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin đối soát',
            ], 404);           
        }

        $doiSoat->update([
            'trang_thai_thanh_toan' => 'da_chuyen_khoan'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu chuyển khoản thành công',
            'data' => $doiSoat,
        ], 200);   
    }
}
