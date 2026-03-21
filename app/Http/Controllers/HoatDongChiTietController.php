<?php

namespace App\Http\Controllers;

use App\Models\HoatDongChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HoatDongChiTietController extends Controller
{
    public function index(Request $request)
    {
        // Kiểm tra khách hàng đăng nhập
        $khachHang = $request->user();
        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        $hoatDongChiTiets = HoatDongChiTiet::with(['keHoach', 'nhom', 'diaDiem'])->get();
        if ($hoatDongChiTiets->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có hoạt động chi tiết nào'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách hoạt động chi tiết thành công',
            'data' => $hoatDongChiTiets
        ], 200);
    }
    public function show(Request $request, $id)
    {
        $khachHang = $request->user();
        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }
        $hoatDongChiTiet = HoatDongChiTiet::with(['keHoach', 'nhom', 'diaDiem'])->find($id);
        if (!$hoatDongChiTiet) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hoạt động chi tiết'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin hoạt động chi tiết thành công',
            'data' => $hoatDongChiTiet
        ], 200);
    }
    public function store(Request $request)
    {
        $khachHang = $request->user();
        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'ma_hoat_dong_chi_tiet' => 'required|unique:hoat_dong_chi_tiet|max:10',
            'ma_ke_hoach' => 'required|exists:ke_hoach,ma_ke_hoach',
            'ma_nhom' => 'required|exists:nhom,Ma_nhom',
            'ma_dia_diem' => 'required|exists:dia_diem,ma_dia_diem',
            'gio_bat_dau' => 'required|date_format:H:i',
            'gio_ket_thuc' => 'required|date_format:H:i|after:gio_bat_dau',
            'ngay_cu_the' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Thêm hoạt động chi tiết thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $hoatDongChiTiet = HoatDongChiTiet::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Thêm hoạt động chi tiết thành công',
                'data' => $hoatDongChiTiet
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm hoạt động chi tiết'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $khachHang = $request->user();
        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        $hoatDongChiTiet = HoatDongChiTiet::find($id);
        if (!$hoatDongChiTiet) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hoạt động chi tiết'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ma_ke_hoach' => 'sometimes|required|exists:ke_hoach,ma_ke_hoach',
            'ma_nhom' => 'sometimes|required|exists:nhom,Ma_nhom',
            'ma_dia_diem' => 'sometimes|required|exists:dia_diem,ma_dia_diem',
            'gio_bat_dau' => 'sometimes|required|date_format:H:i',
            'gio_ket_thuc' => 'sometimes|required|date_format:H:i|after:gio_bat_dau',
            'ngay_cu_the' => 'sometimes|required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật hoạt động chi tiết thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            unset($data['ma_hoat_dong_chi_tiet']);

            $hoatDongChiTiet->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật hoạt động chi tiết thành công',
                'data' => $hoatDongChiTiet
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật hoạt động chi tiết thất bại'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $khachHang = $request->user();
        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        $hoatDongChiTiet = HoatDongChiTiet::find($id);
        if (!$hoatDongChiTiet) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hoạt động chi tiết'
            ], 404);
        }

        try {
            $hoatDongChiTiet->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa hoạt động chi tiết thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa hoạt động chi tiết thất bại'
            ], 500);
        }
    }
}
