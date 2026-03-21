<?php

namespace App\Http\Controllers;

use App\Models\CauHinhNgay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CauHinhNgayController extends Controller
{
    /**
     * Lấy danh sách toàn bộ cấu hình ngày (Admin)
     */
    public function index()
    {
        $cauHinhNgays = CauHinhNgay::all();

        if ($cauHinhNgays->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có cấu hình ngày nào'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách cấu hình ngày thành công',
            'data' => $cauHinhNgays
        ], 200);
    }

    /**
     * Xem chi tiết một cấu hình ngày (Admin)
     */
    public function show($ma_cau_hinh_ngay)
    {
        $cauHinhNgay = CauHinhNgay::find($ma_cau_hinh_ngay);

        if (!$cauHinhNgay) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cấu hình ngày'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin cấu hình ngày thành công',
            'data' => $cauHinhNgay
        ], 200);
    }

    /**
     * Thêm mới cấu hình ngày (Admin)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'ma_cau_hinh_ngay' => 'required|string|unique:cau_hinh_ngay,ma_cau_hinh_ngay|max:10',
            'loai_ngay_le' => 'required|integer|in:1,2,3',
            'ten_ngay_le' => 'nullable|string|max:255',
            'ngay' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Thêm cấu hình ngày thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cauHinhNgay = CauHinhNgay::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Thêm cấu hình ngày thành công',
                'data' => $cauHinhNgay
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm cấu hình ngày'
            ], 500);
        }
    }

    /**
     * Cập nhật cấu hình ngày (Admin)
     */
    public function update(Request $request, $ma_cau_hinh_ngay)
    {
        $cauHinhNgay = CauHinhNgay::find($ma_cau_hinh_ngay);

        if (!$cauHinhNgay) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cấu hình ngày'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'loai_ngay_le' => 'sometimes|required|integer|in:1,2,3',
            'ten_ngay_le' => 'nullable|string|max:255',
            'ngay' => 'sometimes|required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật cấu hình ngày thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cauHinhNgay->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật cấu hình ngày thành công',
                'data' => $cauHinhNgay
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật cấu hình ngày thất bại'
            ], 500);
        }
    }

    /**
     * Xóa cấu hình ngày (Admin)
     */
    public function destroy($ma_cau_hinh_ngay)
    {
        $cauHinhNgay = CauHinhNgay::find($ma_cau_hinh_ngay);

        if (!$cauHinhNgay) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cấu hình ngày'
            ], 404);
        }

        try {
            $cauHinhNgay->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa cấu hình ngày thành công'
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa cấu hình ngày thất bại'
            ], 500);
        }
    }
}
