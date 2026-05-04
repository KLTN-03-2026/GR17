<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNhomRequest;
use App\Http\Requests\UpdateNhomRequest;
use App\Models\Nhom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NhomController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Nhom::query()
            ->withCount('thanhVienNhom')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $group = Nhom::query()
            ->with(['thanhVienNhom.khachHang'])
            ->withCount('thanhVienNhom')
            ->where('Ma_nhom', $id)
            ->first();

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Nhom not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $group,
        ]);
    }

    public function store(StoreNhomRequest $request): JsonResponse
    {
        $group = Nhom::query()->create([
            'ten_nhom' => $request->input('ten_nhom'),
        ]);

        return response()->json([
            'success' => true,
            'data' => $group,
        ], 201);
    }

    public function update(UpdateNhomRequest $request, string $id): JsonResponse
    {
        $group = Nhom::query()->where('Ma_nhom', $id)->first();

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Nhom not found.',
            ], 404);
        }

        $group->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cap nhat nhom thanh cong.',
            'data' => $group,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $group = Nhom::query()->where('Ma_nhom', $id)->first();

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Nhom not found.',
            ], 404);
        }

        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoa nhom thanh cong.',
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $groups = Nhom::query()
            ->withCount('thanhVienNhom')
            ->when($request->filled('Ma_nhom'), function ($query) use ($request) {
                $query->where('Ma_nhom', 'like', '%' . trim((string) $request->query('Ma_nhom')) . '%');
            })
            ->when($request->filled('ten_nhom'), function ($query) use ($request) {
                $query->where('ten_nhom', 'like', '%' . trim((string) $request->query('ten_nhom')) . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Tim kiem nhom thanh cong.',
            'data' => $groups,
        ]);
    }
}
