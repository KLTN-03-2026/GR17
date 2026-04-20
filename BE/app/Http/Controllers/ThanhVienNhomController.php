<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThanhVienNhomRequest;
use App\Http\Requests\UpdateThanhVienNhomRequest;
use App\Models\ThanhVienNhom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThanhVienNhomController extends Controller
{
    public function index(): JsonResponse
    {
        $members = ThanhVienNhom::with(['nhom', 'khachHang'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Lay danh sach thanh vien nhom thanh cong',
            'data' => $members,
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $member = ThanhVienNhom::with(['nhom', 'khachHang'])->find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay thanh vien nhom',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lay thong tin thanh vien nhom thanh cong',
            'data' => $member,
        ]);
    }

    public function getByNhom(string $maNhom): JsonResponse
    {
        $members = ThanhVienNhom::with(['nhom', 'khachHang'])
            ->where('Ma_nhom', $maNhom)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lay danh sach thanh vien theo nhom thanh cong',
            'data' => $members,
        ]);
    }

    public function store(StoreThanhVienNhomRequest $request): JsonResponse
    {
        $member = ThanhVienNhom::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Them thanh vien nhom thanh cong',
            'data' => $member->load(['nhom', 'khachHang']),
        ], 201);
    }

    public function search(Request $request): JsonResponse
    {
        $members = ThanhVienNhom::with(['nhom', 'khachHang'])
            ->when($request->filled('Ma_nhom'), fn ($query) => $query->where('Ma_nhom', $request->query('Ma_nhom')))
            ->when($request->filled('Ma_khach_hang'), fn ($query) => $query->where('Ma_khach_hang', $request->query('Ma_khach_hang')))
            ->when($request->filled('vai_tro'), fn ($query) => $query->where('vai_tro', $request->query('vai_tro')))
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Tim kiem thanh vien nhom thanh cong',
            'data' => $members,
        ]);
    }

    public function update(UpdateThanhVienNhomRequest $request, string $id): JsonResponse
    {
        $member = ThanhVienNhom::find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay thanh vien nhom',
            ], 404);
        }

        $member->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cap nhat thanh vien nhom thanh cong',
            'data' => $member->load(['nhom', 'khachHang']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $member = ThanhVienNhom::find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay thanh vien nhom',
            ], 404);
        }

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoa thanh vien nhom thanh cong',
        ]);
    }
}
