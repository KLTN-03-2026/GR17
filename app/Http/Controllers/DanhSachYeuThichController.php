<?php

namespace App\Http\Controllers;

use App\Models\DanhSachYeuThich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DanhSachYeuThichController extends Controller
{
    /**
     * Lấy danh sách yêu thích của khách hàng đang đăng nhập
     */
    public function indexCustomer(Request $request)
    {
        $khachHang = clone $request->user();
        $maKhachHang = $request->ma_khach_hang ?? ($khachHang ? $khachHang->Ma_khach_hang : null);

        if (!$maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp mã khách hàng hoặc đăng nhập'
            ], 401);
        }

        $danhSach = DanhSachYeuThich::where('ma_khach_hang', $maKhachHang)->get();

        if ($danhSach->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Danh sách yêu thích trống'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách yêu thích thành công',
            'data' => $danhSach
        ], 200);
    }

    /**
     * Thêm địa điểm vào danh sách yêu thích
     */
    public function storeCustomer(Request $request)
    {
        $khachHang = clone $request->user();
        $maKhachHang = $request->ma_khach_hang ?? ($khachHang ? $khachHang->Ma_khach_hang : null);

        if (!$maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp mã khách hàng hoặc đăng nhập để thêm vào danh sách'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'ma_danh_sach_ua_thich' => 'required|unique:danh_sach_yeu_thich|max:10',
            'ma_dia_diem' => 'required|exists:dia_diem,ma_dia_diem'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if already in favorites
        $exists = DanhSachYeuThich::where('ma_khach_hang', $maKhachHang)
            ->where('ma_dia_diem', $request->ma_dia_diem)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm này đã có trong danh sách yêu thích của bạn'
            ], 400);
        }

        try {
            $data = $request->except('ma_khach_hang');
            $data['ma_khach_hang'] = $maKhachHang;

            $danhSach = DanhSachYeuThich::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Thêm vào danh sách yêu thích thành công',
                'data' => $danhSach
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm vào danh sách yêu thích'
            ], 500);
        }
    }

    /**
     * Xóa địa điểm khỏi danh sách yêu thích
     */
    public function destroyCustomer(Request $request, $ma_danh_sach_ua_thich)
    {
        $khachHang = clone $request->user();
        $maKhachHang = $request->ma_khach_hang ?? ($khachHang ? $khachHang->Ma_khach_hang : null);

        if (!$maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp mã khách hàng hoặc đăng nhập'
            ], 401);
        }

        $danhSach = DanhSachYeuThich::find($ma_danh_sach_ua_thich);

        if (!$danhSach) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy mục trong danh sách yêu thích'
            ], 404);
        }

        // Xac nhan dung list cua nguoi do
        if ($danhSach->ma_khach_hang !== $maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể xóa danh sách yêu thích của người khác'
            ], 403);
        }

        try {
            $danhSach->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa khỏi danh sách yêu thích thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa thất bại'
            ], 500);
        }
    }
}
