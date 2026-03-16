<?php

namespace App\Http\Controllers;

use App\Models\ThanhVienNhom;
use Illuminate\Http\Request;

class ThanhVienNhomController extends Controller
{
    public function index()
    {
        $tvn = ThanhVienNhom::with(['nhom', 'khachHang'])->get();
        if ($tvn->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có thành viên nhóm nào'], 404);
        }
        return response()->json(['success' => true, 'data' => $tvn], 200);
    }

    public function show($id)
    {
        $tvn = ThanhVienNhom::with(['nhom', 'khachHang'])->find($id);
        if (!$tvn) {
             return response()->json(['success' => false, 'message' => 'Không tìm thấy thành viên nhóm'], 404);
        }
        return response()->json(['success' => true, 'data' => $tvn], 200);
    }

    public function getByNhom($maNhom)
    {
        $tvn = ThanhVienNhom::with(['khachHang'])->where('Ma_nhom', $maNhom)->get();
        if ($tvn->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Nhóm này chưa có thành viên hoặc không tồn tại'], 404);
        }
        return response()->json(['success' => true, 'data' => $tvn], 200);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Ma_thanh_vien' => 'required|string|unique:thanh_vien_nhom|max:20',
            'Ma_nhom' => 'required|string|exists:nhom,Ma_nhom',
            'Ma_khach_hang' => 'required|string|exists:khach_hang,Ma_khach_hang',
            'vai_tro' => 'nullable|integer|in:0,1',
        ]);
        $tvn = ThanhVienNhom::create($validated);
        return response()->json(['success' => true, 'message' => 'Thêm thành viên thành công', 'data' => $tvn], 201);
    }
    
    public function update(Request $request, $id)
    {
        $tvn = ThanhVienNhom::find($id);
        if (!$tvn) {
             return response()->json(['success' => false, 'message' => 'Không tìm thấy thành viên nhóm'], 404);
        }
        $validated = $request->validate([
            'vai_tro' => 'sometimes|required|integer|in:0,1',
        ]);
        $tvn->update($validated);
        return response()->json(['success' => true, 'message' => 'Cập nhật thành viên thành công', 'data' => $tvn], 200);
    }
    
    public function destroy($id)
    {
        $tvn = ThanhVienNhom::find($id);
        if (!$tvn) {
             return response()->json(['success' => false, 'message' => 'Không tìm thấy thành viên nhóm'], 404);
        }
        $tvn->delete();
        return response()->json(['success' => true, 'message' => 'Xóa thành viên thành công'], 200);
    }
    
    public function search(Request $request)
    {
        $query = ThanhVienNhom::with(['nhom', 'khachHang']);
        if ($request->has('Ma_thanh_vien')) {
            $query->where('Ma_thanh_vien', $request->Ma_thanh_vien);
        }
        if ($request->has('Ma_nhom')) {
            $query->where('Ma_nhom', $request->Ma_nhom);
        }
        if ($request->has('Ma_khach_hang')) {
            $query->where('Ma_khach_hang', $request->Ma_khach_hang);
        }
        $tvn = $query->get();
        if ($tvn->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy kết quả'], 404);
        }
        return response()->json(['success' => true, 'data' => $tvn], 200);
    }
}
