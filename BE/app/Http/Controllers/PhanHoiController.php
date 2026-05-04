<?php

namespace App\Http\Controllers;

use App\Models\PhanHoi;
use Illuminate\Http\Request;

class PhanHoiController extends Controller
{
    /**
     * Khách hàng gửi báo cáo/góp ý
     */
    public function store(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'loai' => 'required|string|max:50',
            'mo_ta' => 'required|string',
            'url_trang_loi' => 'nullable|string',
        ]);

        $phanHoi = new PhanHoi();
        $phanHoi->tieu_de = $request->tieu_de;
        $phanHoi->loai = $request->loai;
        $phanHoi->mo_ta = $request->mo_ta;
        $phanHoi->url_trang_loi = $request->url_trang_loi;
        $phanHoi->trang_thai = 'Mới';
        
        // Gắn ID khách hàng nếu đã đăng nhập
        if (auth('sanctum')->check()) {
            $phanHoi->ma_khach_hang = auth('sanctum')->user()->Ma_khach_hang;
        }

        $phanHoi->save();

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã gửi phản hồi. Chúng tôi sẽ ghi nhận và xử lý sớm nhất.'
        ], 201);
    }

    /**
     * Admin lấy danh sách báo cáo
     */
    public function index(Request $request)
    {
        $query = PhanHoi::with('khachHang')->orderBy('created_at', 'desc');

        // Có thể thêm filter theo trạng thái
        if ($request->has('trang_thai') && $request->trang_thai !== 'all') {
            $query->where('trang_thai', $request->trang_thai);
        }

        $phanHois = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $phanHois
        ]);
    }

    /**
     * Admin cập nhật trạng thái
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|string|in:Mới,Đang xử lý,Đã giải quyết',
        ]);

        $phanHoi = PhanHoi::find($id);
        if (!$phanHoi) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phản hồi'
            ], 404);
        }

        $phanHoi->trang_thai = $request->trang_thai;
        $phanHoi->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công'
        ]);
    }
}
