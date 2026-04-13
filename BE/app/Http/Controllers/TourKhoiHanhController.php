<?php

namespace App\Http\Controllers;

use App\Models\TourKhoiHanh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreTourKhoiHanhRequest;
use App\Http\Requests\UpdateTourKhoiHanhRequest;

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

    public function store(StoreTourKhoiHanhRequest $request)
    {
        try {
            $tourKhoiHanh = TourKhoiHanh::create($request->validated());

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

    public function update(UpdateTourKhoiHanhRequest $request, $ma_thoi_gian_tour)
    {
        $tourKhoiHanh = TourKhoiHanh::find($ma_thoi_gian_tour);

        if (!$tourKhoiHanh) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dữ liệu khởi hành tour'
            ], 404);
        }

        try {
            $tourKhoiHanh->update($request->validated());

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
