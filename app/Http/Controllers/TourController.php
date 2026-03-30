<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\UpdateTourRequest;

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

    public function store(StoreTourRequest $request)
    {
        try {
            $tour = Tour::create($request->validated());

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

    public function update(UpdateTourRequest $request, $ma_tour)
    {
        $tour = Tour::find($ma_tour);

        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tour'
            ], 404);
        }

        try {
            $tour->update($request->validated());

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
