<?php

namespace App\Http\Controllers;

use App\Models\ChiTietTour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChiTietTourController extends Controller
{
    public function index()
    {
        $chiTietTours = ChiTietTour::all();

        if ($chiTietTours->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có chi tiết tour nào'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách chi tiết tour thành công',
            'data' => $chiTietTours
        ], 200);
    }

    public function show($ma_chi_tiet_tour)
    {
        $chiTietTour = ChiTietTour::find($ma_chi_tiet_tour);

        if (!$chiTietTour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chi tiết tour'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin chi tiết tour thành công',
            'data' => $chiTietTour
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'ma_chi_tiet_tour' => 'required|unique:chi_tiet_tours|max:10',
            'ma_tour' => 'required|max:10',
            'ma_dia_diem' => 'required|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Thêm chi tiết tour thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $chiTietTour = ChiTietTour::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Thêm chi tiết tour thành công',
                'data' => $chiTietTour
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm chi tiết tour'
            ], 500);
        }
    }

    public function update(Request $request, $ma_chi_tiet_tour)
    {
        $chiTietTour = ChiTietTour::find($ma_chi_tiet_tour);

        if (!$chiTietTour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chi tiết tour'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ma_tour' => 'sometimes|required|max:10',
            'ma_dia_diem' => 'sometimes|required|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật chi tiết tour thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $chiTietTour->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật chi tiết tour thành công',
                'data' => $chiTietTour
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật chi tiết tour thất bại'
            ], 500);
        }
    }

    public function destroy($ma_chi_tiet_tour)
    {
        $chiTietTour = ChiTietTour::find($ma_chi_tiet_tour);

        if (!$chiTietTour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chi tiết tour'
            ], 404);
        }

        try {
            $chiTietTour->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa chi tiết tour thành công'
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa chi tiết tour thất bại.'
            ], 500);
        }
    }
}
