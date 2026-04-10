<?php

namespace App\Http\Controllers;

use App\Models\DanhGiaKeHoach;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDanhGiaKeHoachRequest;
use App\Http\Requests\UpdateDanhGiaKeHoachRequest;

class DanhGiaKeHoachController extends Controller
{
    public function index()
    {
        $danhGias = DanhGiaKeHoach::with(['khachHang', 'diaDiem'])->get();
        if ($danhGias->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có đánh giá nào'], 404);
        }
        return response()->json(['success' => true, 'data' => $danhGias], 200);
    }

    public function show($id)
    {
        $danhGia = DanhGiaKeHoach::with(['khachHang', 'diaDiem'])->find($id);
        if (!$danhGia) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đánh giá'], 404);
        }
        return response()->json(['success' => true, 'data' => $danhGia], 200);
    }

    public function getByDiaDiem($maDiaDiem)
    {
        $danhGias = DanhGiaKeHoach::with('khachHang')
            ->where('ma_dia_diem', $maDiaDiem)
            ->get();
        if ($danhGias->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Địa điểm này chưa có đánh giá nào hoặc không tồn tại'], 404);
        }
        return response()->json(['success' => true, 'data' => $danhGias], 200);
    }

    public function store(StoreDanhGiaKeHoachRequest $request)
    {
        $danhGia = DanhGiaKeHoach::create($request->validated());
        return response()->json(['success' => true, 'message' => 'Thêm đánh giá thành công', 'data' => $danhGia], 201);
    }

    public function update(UpdateDanhGiaKeHoachRequest $request, $id)
    {
        $danhGia = DanhGiaKeHoach::find($id);
        if (!$danhGia) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đánh giá'], 404);
        }
        $danhGia->update($request->validated());
        return response()->json(['success' => true, 'message' => 'Cập nhật đánh giá thành công', 'data' => $danhGia], 200);
    }

    public function destroy($id)
    {
        $danhGia = DanhGiaKeHoach::find($id);
        if (!$danhGia) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đánh giá'], 404);
        }
        $danhGia->delete();
        return response()->json(['success' => true, 'message' => 'Xóa đánh giá thành công'], 200);
    }

    public function search(Request $request)
    {
        $query = DanhGiaKeHoach::with(['khachHang', 'diaDiem']);
        if ($request->has('Ma_danh_gia')) {
            $query->where('Ma_danh_gia', $request->Ma_danh_gia);
        }
        if ($request->has('Ma_khach_hang')) {
            $query->where('Ma_khach_hang', $request->Ma_khach_hang);
        }
        if ($request->has('ma_dia_diem')) {
            $query->where('ma_dia_diem', $request->ma_dia_diem);
        }
        if ($request->has('so_sao')) {
            $query->where('so_sao', $request->so_sao);
        }
        $danhGias = $query->get();
        if ($danhGias->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy kết quả'], 404);
        }
        return response()->json(['success' => true, 'data' => $danhGias], 200);
    }
}
