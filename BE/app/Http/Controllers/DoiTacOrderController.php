<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use App\Models\Tour;
use Illuminate\Http\Request;

class DoiTacOrderController extends Controller
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

        // Fetch all Tours owned by this partner
        $tourIds = Tour::where('ma_doi_tac', $maDoiTac)->pluck('ma_tour');

        // Fetch HoaDons that purchased these tours (loai_hoa_don = 0 is Tour)
        $hoaDons = HoaDon::with(['nhom'])
            ->whereIn('ma_doi_tuong', $tourIds)
            ->where('loai_hoa_don', 0)
            ->orderByDesc('ngay_tao')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách đơn hàng thành công',
            'data' => $hoaDons
        ], 200);
    }
}
