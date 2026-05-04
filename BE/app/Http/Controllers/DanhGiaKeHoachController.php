<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDanhGiaKeHoachRequest;
use App\Http\Requests\UpdateDanhGiaKeHoachRequest;
use App\Models\DanhGiaKeHoach;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function search(Request $request): JsonResponse
    {
        $reviews = DanhGiaKeHoach::query()
            ->with(['khachHang', 'diaDiem'])
            ->when($request->filled('Ma_khach_hang'), function ($query) use ($request) {
                $query->where('Ma_khach_hang', $request->input('Ma_khach_hang'));
            })
            ->when($request->filled('ma_dia_diem'), function ($query) use ($request) {
                $query->where('ma_dia_diem', $request->input('ma_dia_diem'));
            })
            ->when($request->filled('so_sao'), function ($query) use ($request) {
                $query->where('so_sao', $request->integer('so_sao'));
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Tim kiem danh gia thanh cong',
            'data' => $reviews,
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $query = DanhGiaKeHoach::query()
            ->when($request->filled('ma_dia_diem'), function ($query) use ($request) {
                $query->where('ma_dia_diem', $request->input('ma_dia_diem'));
            });

        $ratingCounts = (clone $query)
            ->selectRaw('so_sao, COUNT(*) as total')
            ->groupBy('so_sao')
            ->orderBy('so_sao')
            ->pluck('total', 'so_sao');

        $distribution = collect(range(1, 5))
            ->mapWithKeys(fn ($star) => [$star => (int) ($ratingCounts[$star] ?? 0)]);

        return response()->json([
            'success' => true,
            'message' => 'Thong ke danh gia thanh cong',
            'data' => [
                'total_reviews' => (clone $query)->count(),
                'average_rating' => round((float) ((clone $query)->avg('so_sao') ?? 0), 2),
                'rating_distribution' => $distribution,
            ],
        ]);
    }

    public function getByDiaDiem(string $maDiaDiem): JsonResponse
    {
        $reviews = DanhGiaKeHoach::query()
            ->with(['khachHang', 'diaDiem'])
            ->where('ma_dia_diem', $maDiaDiem)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lay danh gia theo dia diem thanh cong',
            'data' => $reviews,
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

    public function update(UpdateDanhGiaKeHoachRequest $request, string $id): JsonResponse
    {
        $review = DanhGiaKeHoach::query()->find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay danh gia',
            ], 404);
        }

        $review->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cap nhat danh gia thanh cong',
            'data' => $review->load(['khachHang', 'diaDiem']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $review = DanhGiaKeHoach::query()->find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay danh gia',
            ], 404);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoa danh gia thanh cong',
        ]);
    }
}
