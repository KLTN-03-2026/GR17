<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use Illuminate\Http\Request;

class HoaDonController extends Controller
{
    public function index()
    {
        $hoaDons = HoaDon::with(['khachHang', 'nhom', 'diaDiem'])->get();
        if ($hoaDons->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có hóa đơn nào'], 404);
        }
        return response()->json(['success' => true, 'data' => $hoaDons], 200);
    }

    public function show($id)
    {
        $hoaDon = HoaDon::with(['khachHang', 'nhom', 'diaDiem'])->find($id);
        if (!$hoaDon) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn'], 404);
        }
        return response()->json(['success' => true, 'data' => $hoaDon], 200);
    }

    public function getByNguoiDung($maKhachHang)
    {
        $hoaDons = HoaDon::with(['nhom', 'diaDiem'])
                         ->where('Ma_khach_hang', $maKhachHang)
                         ->get();
        if ($hoaDons->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Khách hàng này chưa có hóa đơn nào hoặc không tồn tại'], 404);
        }
        return response()->json(['success' => true, 'data' => $hoaDons], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Ma_hoa_don'   => 'required|string|unique:hoa_don|max:20',
            'Ma_khach_hang' => 'required|string|exists:khach_hang,Ma_khach_hang',
            'Ma_nhom'       => 'nullable|string|exists:nhom,Ma_nhom',
            'ma_dia_diem'   => 'required|string|exists:dia_diem,ma_dia_diem',
            'tong_tien'     => 'nullable|numeric|min:0',
            'trang_thai'    => 'nullable|integer|in:0,1,2',
            'ngay_dat'      => 'nullable|date',
        ]);
        $hoaDon = HoaDon::create($validated);
        return response()->json(['success' => true, 'message' => 'Thêm hóa đơn thành công', 'data' => $hoaDon], 201);
    }

    public function update(Request $request, $id)
    {
        $hoaDon = HoaDon::find($id);
        if (!$hoaDon) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn'], 404);
        }
        $validated = $request->validate([
            'tong_tien'  => 'sometimes|required|numeric|min:0',
            'trang_thai' => 'sometimes|required|integer|in:0,1,2',
        ]);
        $hoaDon->update($validated);
        return response()->json(['success' => true, 'message' => 'Cập nhật hóa đơn thành công', 'data' => $hoaDon], 200);
    }

    public function destroy($id)
    {
        $hoaDon = HoaDon::find($id);
        if (!$hoaDon) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn'], 404);
        }
        $hoaDon->delete();
        return response()->json(['success' => true, 'message' => 'Xóa hóa đơn thành công'], 200);
    }

    public function search(Request $request)
    {
        $query = HoaDon::with(['khachHang', 'nhom', 'diaDiem']);
        if ($request->has('Ma_hoa_don')) {
            $query->where('Ma_hoa_don', $request->Ma_hoa_don);
        }
        if ($request->has('Ma_khach_hang')) {
            $query->where('Ma_khach_hang', $request->Ma_khach_hang);
        }
        if ($request->has('Ma_nhom')) {
            $query->where('Ma_nhom', $request->Ma_nhom);
        }
        if ($request->has('ma_dia_diem')) {
            $query->where('ma_dia_diem', $request->ma_dia_diem);
        }
        if ($request->has('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        $hoaDons = $query->get();
        if ($hoaDons->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy kết quả'], 404);
        }
        return response()->json(['success' => true, 'data' => $hoaDons], 200);
    }
}
