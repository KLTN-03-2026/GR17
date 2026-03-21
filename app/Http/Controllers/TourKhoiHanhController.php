<?php

namespace App\Http\Controllers;

use App\Models\TourKhoiHanh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TourKhoiHanhController extends Controller
{
    public function index()
    {
        $tourKhoiHanhs = TourKhoiHanh::all();

        if ($tourKhoiHanhs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có dữ liệu khởi hành tour nào'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách khởi hành tour thành công',
            'data' => $tourKhoiHanhs
        ], 200);
    }

    public function show($ma_thoi_gian_tour)
    {
        $tourKhoiHanh = TourKhoiHanh::find($ma_thoi_gian_tour);

        if (!$tourKhoiHanh) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dữ liệu khởi hành tour'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin khởi hành tour thành công',
            'data' => $tourKhoiHanh
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'ma_thoi_gian_tour' => 'required|unique:tour_khoi_hanhs|max:10',
            'ma_tour' => 'required|max:10',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'so_cho' => 'nullable|integer',
            'tinh_trang' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Thêm dữ liệu khởi hành thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tourKhoiHanh = TourKhoiHanh::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Thêm dữ liệu khởi hành thành công',
                'data' => $tourKhoiHanh
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm dữ liệu khởi hành'
            ], 500);
        }
    }

    public function update(Request $request, $ma_thoi_gian_tour)
    {
        $tourKhoiHanh = TourKhoiHanh::find($ma_thoi_gian_tour);

        if (!$tourKhoiHanh) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dữ liệu khởi hành tour'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ma_tour' => 'sometimes|required|max:10',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'so_cho' => 'nullable|integer',
            'tinh_trang' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật dữ liệu khởi hành thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tourKhoiHanh->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật dữ liệu khởi hành thành công',
                'data' => $tourKhoiHanh
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật dữ liệu khởi hành thất bại'
            ], 500);
        }
    }

    public function destroy($ma_thoi_gian_tour)
    {
        $tourKhoiHanh = TourKhoiHanh::find($ma_thoi_gian_tour);

        if (!$tourKhoiHanh) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dữ liệu khởi hành tour'
            ], 404);
        }

        try {
            $tourKhoiHanh->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa dữ liệu khởi hành tour thành công'
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa dữ liệu khởi hành thất bại.'
            ], 500);
        }
    }
}
