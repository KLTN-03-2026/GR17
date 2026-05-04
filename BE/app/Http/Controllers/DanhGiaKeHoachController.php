<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDanhGiaKeHoachRequest;
use App\Models\DanhGiaKeHoach;
use Illuminate\Http\JsonResponse;

class DanhGiaKeHoachController extends Controller
{
    public function index(): JsonResponse
    {
        $reviews = DanhGiaKeHoach::query()
            ->with(['khachHang', 'diaDiem'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lay danh sach danh gia thanh cong',
            'data' => $reviews,
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $review = DanhGiaKeHoach::query()
            ->with(['khachHang', 'diaDiem'])
            ->find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay danh gia',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lay thong tin danh gia thanh cong',
            'data' => $review,
        ]);
    }

    public function store(StoreDanhGiaKeHoachRequest $request): JsonResponse
    {
        $review = DanhGiaKeHoach::query()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Them danh gia thanh cong',
            'data' => $review->load(['khachHang', 'diaDiem']),
        ], 201);
    }
}
