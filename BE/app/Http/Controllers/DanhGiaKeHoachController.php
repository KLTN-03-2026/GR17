<?php

namespace App\Http\Controllers;

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
}
