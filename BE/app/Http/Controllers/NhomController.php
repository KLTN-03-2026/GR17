<?php

namespace App\Http\Controllers;

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
}
