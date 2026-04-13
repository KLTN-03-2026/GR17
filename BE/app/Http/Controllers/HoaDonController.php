<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreHoaDonRequest;
use App\Http\Requests\UpdateHoaDonRequest;

class HoaDonController extends Controller
{
    public function index()
    {
        $hoaDons = HoaDon::with('nhom')->get();

        if ($hoaDons->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có hóa đơn nào'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách hóa đơn thành công',
            'data' => $hoaDons
        ], 200);
    }

    public function show($ma_hoa_don)
    {
        $hoaDon = HoaDon::with('nhom')->find($ma_hoa_don);

        if (!$hoaDon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin hóa đơn thành công',
            'data' => $hoaDon
        ], 200);
    }

    public function store(StoreHoaDonRequest $request)
    {
        try {
            $hoaDon = HoaDon::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Thêm hóa đơn thành công',
                'data' => $hoaDon
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm hóa đơn'
            ], 500);
        }
    }

    public function update(UpdateHoaDonRequest $request, $ma_hoa_don)
    {
        $hoaDon = HoaDon::find($ma_hoa_don);

        if (!$hoaDon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ], 404);
        }

        try {
            $hoaDon->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật hóa đơn thành công',
                'data' => $hoaDon
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật hóa đơn thất bại'
            ], 500);
        }
    }

    public function destroy($ma_hoa_don)
    {
        $hoaDon = HoaDon::find($ma_hoa_don);

        if (!$hoaDon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ], 404);
        }

        try {
            $hoaDon->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa hóa đơn thành công'
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa hóa đơn thất bại. Có thể hóa đơn đang được sử dụng.'
            ], 500);
        }
    }
}