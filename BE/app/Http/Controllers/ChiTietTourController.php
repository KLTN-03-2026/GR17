<?php

namespace App\Http\Controllers;

use App\Models\ChiTietTour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreChiTietTourRequest;
use App\Http\Requests\UpdateChiTietTourRequest;

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

    public function store(StoreChiTietTourRequest $request)
    {
        try {
            $chiTietTour = ChiTietTour::create($request->validated());

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

    public function update(UpdateChiTietTourRequest $request, $ma_chi_tiet_tour)
    {
        $chiTietTour = ChiTietTour::find($ma_chi_tiet_tour);

        if (!$chiTietTour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chi tiết tour'
            ], 404);
        }

        try {
            $chiTietTour->update($request->validated());

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
                'message' => 'Xóa chi tiết tour thất bại. Có thể chi tiết tour đang được sử dụng.'
            ], 500);
        }
    }
}
