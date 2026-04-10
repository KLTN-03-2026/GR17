<?php

namespace App\Http\Controllers;

use App\Models\HoatDongChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreHoatDongChiTietRequest;
use App\Http\Requests\UpdateHoatDongChiTietRequest;

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
    public function store(StoreHoatDongChiTietRequest $request)
    {
        $khachHang = $request->user();
        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        try {
            $hoatDongChiTiet = HoatDongChiTiet::create($request->validated());

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

    public function update(UpdateHoatDongChiTietRequest $request, $id)
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
            $hoatDongChiTiet->update($request->validated());

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
