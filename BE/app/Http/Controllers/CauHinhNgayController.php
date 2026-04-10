<?php

namespace App\Http\Controllers;

use App\Models\CauHinhNgay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreCauHinhNgayRequest;
use App\Http\Requests\UpdateCauHinhNgayRequest;

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
    public function store(StoreCauHinhNgayRequest $request)
    {
        try {
            $cauHinhNgay = CauHinhNgay::create($request->validated());

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
    public function update(UpdateCauHinhNgayRequest $request, $ma_cau_hinh_ngay)
    {
        $cauHinhNgay = CauHinhNgay::find($ma_cau_hinh_ngay);

        if (!$cauHinhNgay) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cấu hình ngày'
            ], 404);
        }

        try {
            $cauHinhNgay->update($request->validated());

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
