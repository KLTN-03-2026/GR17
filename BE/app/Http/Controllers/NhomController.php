<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNhomRequest;
use App\Http\Requests\UpdateNhomRequest;
use App\Models\Nhom;
use Illuminate\Http\JsonResponse;

class NhomController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Nhom::query()->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $group = Nhom::query()->where('Ma_nhom', $id)->first();

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
}
