<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThanhVienNhomRequest;
use App\Models\ThanhVienNhom;
use Illuminate\Http\JsonResponse;

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
}
