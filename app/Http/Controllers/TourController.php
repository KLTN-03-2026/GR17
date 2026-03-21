<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::all();

        if ($tours->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có tour nào'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách tour thành công',
            'data' => $tours
        ], 200);
    }

    public function show($ma_tour)
    {
        $tour = Tour::find($ma_tour);

        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tour'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin tour thành công',
            'data' => $tour
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'ma_tour' => 'required|unique:tours|max:10',
            'ten_tour' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|string',
            'so_tien' => 'nullable|numeric',
            'so_ngay' => 'nullable|integer',
            'so_nguoi' => 'nullable|integer',
            'ma_tag' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Thêm tour thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tour = Tour::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Thêm tour thành công',
                'data' => $tour
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm tour'
            ], 500);
        }
    }

    public function update(Request $request, $ma_tour)
    {
        $tour = Tour::find($ma_tour);

        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tour'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ten_tour' => 'sometimes|required|string|max:255',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|string',
            'so_tien' => 'nullable|numeric',
            'so_ngay' => 'nullable|integer',
            'so_nguoi' => 'nullable|integer',
            'ma_tag' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật tour thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tour->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tour thành công',
                'data' => $tour
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật tour thất bại'
            ], 500);
        }
    }

    public function destroy($ma_tour)
    {
        $tour = Tour::find($ma_tour);

        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tour'
            ], 404);
        }

        try {
            $tour->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa tour thành công'
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa tour thất bại. Có thể tour đang được sử dụng.'
            ], 500);
        }
    }
}
