<?php

namespace App\Http\Controllers;

use App\Models\Nhom;
use Illuminate\Http\Request;

class NhomController extends Controller
{
    public function index()
    {
        $nhoms = Nhom::all();
        if ($nhoms->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có nhóm nào'], 404);
        }
        return response()->json(['success' => true, 'data' => $nhoms], 200);
    }

    public function show($id)
    {
        $nhom = Nhom::find($id);
        if (!$nhom) {
             return response()->json(['success' => false, 'message' => 'Không tìm thấy nhóm'], 404);
        }
        return response()->json(['success' => true, 'data' => $nhom], 200);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Ma_nhom' => 'required|string|unique:nhom|max:20',
            'ten_nhom' => 'required|string|max:100',
        ]);
        $nhom = Nhom::create($validated);
        return response()->json(['success' => true, 'message' => 'Thêm nhóm thành công', 'data' => $nhom], 201);
    }
    
    public function update(Request $request, $id)
    {
        $nhom = Nhom::find($id);
        if (!$nhom) {
             return response()->json(['success' => false, 'message' => 'Không tìm thấy nhóm'], 404);
        }
        $validated = $request->validate([
            'ten_nhom' => 'sometimes|required|string|max:100',
        ]);
        $nhom->update($validated);
        return response()->json(['success' => true, 'message' => 'Cập nhật nhóm thành công', 'data' => $nhom], 200);
    }
    
    public function destroy($id)
    {
        $nhom = Nhom::find($id);
        if (!$nhom) {
             return response()->json(['success' => false, 'message' => 'Không tìm thấy nhóm'], 404);
        }
        $nhom->delete();
        return response()->json(['success' => true, 'message' => 'Xóa nhóm thành công'], 200);
    }
    
    public function search(Request $request)
    {
        $query = Nhom::query();
        if ($request->has('Ma_nhom')) {
            $query->where('Ma_nhom', 'like', '%' . $request->Ma_nhom . '%');
        }
        if ($request->has('ten_nhom')) {
            $query->where('ten_nhom', 'like', '%' . $request->ten_nhom . '%');
        }
        $nhoms = $query->get();
        if ($nhoms->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy kết quả'], 404);
        }
        return response()->json(['success' => true, 'data' => $nhoms], 200);
    }
}
