<?php

namespace App\Http\Controllers;

use App\Models\XeKeHoach;
use App\Models\KeHoach;
use App\Models\Xe;
use App\Http\Requests\StoreXeKeHoachRequest;
use App\Http\Requests\UpdateXeKeHoachRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class XeKeHoachController extends Controller
{
    /**
     * Display a listing of vehicles in plans (public access)
     */
    public function index(): JsonResponse
    {
        $xeKeHoach = XeKeHoach::with(['keHoach', 'xe'])
            ->orderBy('ma_xe_ke_hoach')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $xeKeHoach,
            'total' => count($xeKeHoach),
        ]);
    }

    /**
     * Display all vehicle plans with pagination (admin only)
     */
    public function indexAll(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $xeKeHoach = XeKeHoach::with(['keHoach', 'xe'])
            ->orderBy('ma_xe_ke_hoach', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $xeKeHoach->items(),
            'pagination' => [
                'current_page' => $xeKeHoach->currentPage(),
                'per_page' => $xeKeHoach->perPage(),
                'total' => $xeKeHoach->total(),
                'last_page' => $xeKeHoach->lastPage(),
            ],
        ]);
    }

    /**
     * Search vehicle plans by plan or vehicle (admin only)
     */
    public function search(Request $request): JsonResponse
    {
        $searchType = $request->query('type', 'all'); // all, ke_hoach, xe
        $keyword = $request->query('keyword');

        if (!$keyword || strlen($keyword) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Từ khóa tìm kiếm không được để trống',
            ], 400);
        }

        $query = XeKeHoach::with(['keHoach', 'xe']);

        if ($searchType === 'ke_hoach') {
            $query->whereHas('keHoach', function ($q) use ($keyword) {
                $q->where('ten_ke_hoach', 'like', '%' . $keyword . '%');
            });
        }
        elseif ($searchType === 'xe') {
            $query->whereHas('xe', function ($q) use ($keyword) {
                $q->where('ten_xe', 'like', '%' . $keyword . '%')
                    ->orWhere('loai_xe', 'like', '%' . $keyword . '%');
            });
        }
        else {
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('keHoach', function ($subQ) use ($keyword) {
                        $subQ->where('ten_ke_hoach', 'like', '%' . $keyword . '%');
                    }
                    )
                        ->orWhereHas('xe', function ($subQ) use ($keyword) {
                    $subQ->where('ten_xe', 'like', '%' . $keyword . '%')
                        ->orWhere('loai_xe', 'like', '%' . $keyword . '%');
                }
                );
            });
        }
        $xeKeHoach = $query->get();
        return response()->json([
            'success' => true,
            'data' => $xeKeHoach,
            'total' => count($xeKeHoach),
        ]);
    }

    /**
     * Display the specified vehicle plan
     */
    public function show($ma_xe_ke_hoach): JsonResponse
    {
        $xeKeHoach = XeKeHoach::with(['keHoach', 'xe'])->find($ma_xe_ke_hoach);

        if (!$xeKeHoach) {
            return response()->json([
                'success' => false,
                'message' => 'Xe trong kế hoạch không tồn tại',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $xeKeHoach,
        ]);
    }

    /**
     * Store a newly created vehicle plan (admin only)
     */
    public function store(StoreXeKeHoachRequest $request): JsonResponse
    {
        try {
            // Check if combination already exists
            $existing = XeKeHoach::where('ma_ke_hoach', $request->ma_ke_hoach)
                ->where('ma_xe', $request->ma_xe)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Xe này đã có trong kế hoạch',
                ], 409);
            }

            $xeKeHoach = XeKeHoach::create($request->validated());

            $xeKeHoach = $xeKeHoach->load(['keHoach', 'xe']);

            return response()->json([
                'success' => true,
                'message' => 'Thêm xe vào kế hoạch thành công',
                'data' => $xeKeHoach,
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm xe vào kế hoạch: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified vehicle plan (admin only)
     */
    public function update(UpdateXeKeHoachRequest $request, $ma_xe_ke_hoach): JsonResponse
    {
        $xeKeHoach = XeKeHoach::find($ma_xe_ke_hoach);

        if (!$xeKeHoach) {
            return response()->json([
                'success' => false,
                'message' => 'Xe trong kế hoạch không tồn tại',
            ], 404);
        }

        try {
            $xeKeHoach->update($request->validated());
            $xeKeHoach = $xeKeHoach->load(['keHoach', 'xe']);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật xe trong kế hoạch thành công',
                'data' => $xeKeHoach,
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật xe trong kế hoạch: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete the specified vehicle plan (admin only)
     */
    public function destroy($ma_xe_ke_hoach): JsonResponse
    {
        $xeKeHoach = XeKeHoach::find($ma_xe_ke_hoach);

        if (!$xeKeHoach) {
            return response()->json([
                'success' => false,
                'message' => 'Xe trong kế hoạch không tồn tại',
            ], 404);
        }

        try {
            $xeKeHoach->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xoá xe khỏi kế hoạch thành công',
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xoá xe khỏi kế hoạch: ' . $e->getMessage(),
            ], 500);
        }
    }
}
