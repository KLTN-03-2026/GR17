<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreHoaDonRequest;
use App\Http\Requests\UpdateHoaDonRequest;
use App\Http\Requests\UpdateHoaDonStatusRequest;

class HoaDonController extends Controller
{
    public function index(Request $request)
    {
        $query = HoaDon::query()
            ->with('nhom')
            ->when($request->filled('ma_nhom'), function ($query) use ($request) {
                $query->where('ma_nhom', $request->input('ma_nhom'));
            })
            ->when($request->filled('loai_hoa_don'), function ($query) use ($request) {
                $query->where('loai_hoa_don', $request->integer('loai_hoa_don'));
            })
            ->when($request->filled('trang_thai_thanh_toan'), function ($query) use ($request) {
                $query->where('trang_thai_thanh_toan', $request->integer('trang_thai_thanh_toan'));
            })
            ->when($request->filled('ma_doi_tuong'), function ($query) use ($request) {
                $query->where('ma_doi_tuong', $request->input('ma_doi_tuong'));
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('ngay_tao', '>=', $request->input('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('ngay_tao', '<=', $request->input('date_to'));
            })
            ->orderBy('ngay_tao', 'desc')
            ->orderBy('created_at', 'desc');

        $hoaDons = $request->filled('per_page')
            ? $query->paginate(max(1, min($request->integer('per_page'), 100)))
            : $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Lay danh sach hoa don thanh cong',
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

    public function updateStatus(UpdateHoaDonStatusRequest $request, $ma_hoa_don)
    {
        $hoaDon = HoaDon::find($ma_hoa_don);

        if (!$hoaDon) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay hoa don'
            ], 404);
        }

        $hoaDon->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cap nhat trang thai hoa don thanh cong',
            'data' => $hoaDon->load('nhom')
        ], 200);
    }

    public function getByNhom($maNhom)
    {
        $hoaDons = HoaDon::query()
            ->with('nhom')
            ->where('ma_nhom', $maNhom)
            ->orderBy('ngay_tao', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lay hoa don theo nhom thanh cong',
            'data' => $hoaDons
        ], 200);
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
