<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\HoaDon;
use App\Models\ThanhVienNhom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HoaDonController extends Controller
{
    // ============================================
    // API CỦA ADMIN
    // ============================================

    public function indexAdmin(Request $request)
    {
        // $admin = auth('sanctum')->user();

        // if (!$admin instanceof Admin) {
        //     return response()->json(['success' => false, 'message' => 'Bạn không có quyền'], 403);
        // }

        $hoaDons = HoaDon::with(['nhom'])->get();
        if ($hoaDons->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có hóa đơn nào'], 404);
        }
        return response()->json(['success' => true, 'message' => 'Lấy danh sách hóa đơn thành công', 'data' => $hoaDons], 200);
    }

    public function showAdmin($ma_hoa_don)
    {
        $hoaDon = HoaDon::with(['nhom'])->find($ma_hoa_don);
        if (!$hoaDon) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn'], 404);
        }
        return response()->json(['success' => true, 'message' => 'Lấy hóa đơn thành công', 'data' => $hoaDon], 200);
    }

    public function updateStatusAdmin(Request $request, $ma_hoa_don)
    {
        $hoaDon = HoaDon::find($ma_hoa_don);
        if (!$hoaDon) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn'], 404);
        }

        $validator = Validator::make($request->all(), [
            'trang_thai_thanh_toan' => 'required|integer|in:0,1,2',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $hoaDon->update(['trang_thai_thanh_toan' => $request->trang_thai_thanh_toan]);
            return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái thanh toán thành công', 'data' => $hoaDon], 200);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi khi cập nhật'], 500);
        }
    }


    // ============================================
    // API CỦA KHÁCH HÀNG
    // ============================================

    public function indexCustomer(Request $request)
    {
        $khachHang = clone $request->user();
        $maKhachHang = $request->ma_khach_hang ?? ($khachHang ? $khachHang->Ma_khach_hang : null);

        if (!$maKhachHang) {
            return response()->json(['success' => false, 'message' => 'Vui lòng cung cấp mã khách hàng hoặc đăng nhập'], 401);
        }

        $maNhoms = ThanhVienNhom::where('Ma_khach_hang', $maKhachHang)->pluck('Ma_nhom');

        $hoaDons = HoaDon::with(['nhom'])->whereIn('ma_nhom', $maNhoms)->get();
        if ($hoaDons->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có hóa đơn nào'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Lấy danh sách thành công', 'data' => $hoaDons], 200);
    }

    public function showCustomer(Request $request, $ma_hoa_don)
    {
        $khachHang = clone $request->user();
        $maKhachHang = $request->ma_khach_hang ?? ($khachHang ? $khachHang->Ma_khach_hang : null);

        $hoaDon = HoaDon::with(['nhom'])->find($ma_hoa_don);
        if (!$hoaDon) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn'], 404);
        }

        if (!$maKhachHang) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xem hóa đơn này do thiếu xác thực'], 403);
        }

        // Kiem tra thuoc nhom
        $isMember = ThanhVienNhom::where('Ma_khach_hang', $maKhachHang)
            ->where('Ma_nhom', $hoaDon->ma_nhom)
            ->exists();

        if (!$isMember) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xem hóa đơn của nhóm này'], 403);
        }

        return response()->json(['success' => true, 'message' => 'Lấy thông tin thành công', 'data' => $hoaDon], 200);
    }

    public function storeCustomer(Request $request)
    {
        $khachHang = clone $request->user();
        $maKhachHang = $request->ma_khach_hang ?? ($khachHang ? $khachHang->Ma_khach_hang : null);

        $validator = Validator::make($request->all(), [
            'ma_hoa_don' => 'required|unique:hoa_don,ma_hoa_don|max:10',
            'ma_nhom' => 'required|exists:nhom,Ma_nhom|max:20',
            'loai_hoa_don' => 'required|integer|in:0,1',
            'ma_doi_tuong' => 'required|string',
            'tong_tien' => 'required|numeric|min:0',
            'ma_giao_dich' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if (!$maKhachHang) {
            return response()->json(['success' => false, 'message' => 'Bạn phải xác thực khách hàng (hoặc truyền ma_khach_hang) để thêm mới'], 403);
        }

        $isMember = ThanhVienNhom::where('Ma_khach_hang', $maKhachHang)
            ->where('Ma_nhom', $request->ma_nhom)
            ->exists();

        if (!$isMember) {
            return response()->json(['success' => false, 'message' => 'Bạn không nằm trong nhóm này để có quyền thêm hóa đơn'], 403);
        }

        try {
            $data = $request->except(['ma_khach_hang']); // bỏ đi trường ma_khach_hang truyền hờ 
            $data['trang_thai_thanh_toan'] = 0; // Luôn chờ xử lý lúc mới đặt
            $data['ngay_tao'] = now();

            $hoaDon = HoaDon::create($data);
            return response()->json(['success' => true, 'message' => 'Thêm hóa đơn thành công', 'data' => $hoaDon], 201);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi khi thêm hóa đơn. ' . $e->getMessage()], 500);
        }
    }
}
