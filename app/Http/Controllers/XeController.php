<?php

namespace App\Http\Controllers;

use App\Models\Xe;
use App\Http\Requests\StoreXeRequest;
use App\Http\Requests\UpdateXeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class XeController extends Controller
{
    /**
     * Display a listing of active vehicles (public access)
     */
    public function index(): JsonResponse
    {
        $xe = Xe::where('trang_thai', 1)
            ->orderBy('ten_xe')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $xe,
            'total' => count($xe),
        ]);
    }

    /**
     * Display all vehicles (admin only)
     */
    public function indexAll(): JsonResponse
    {
        $xe = Xe::orderBy('ma_xe', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $xe,
            'total' => count($xe),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $searchType = $request->query('type', 'all'); 
        $keyword = $request->query('keyword');

        if (!$keyword || strlen($keyword) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Từ khóa tìm kiếm không được để trống',
            ], 400);
        }

        $query = Xe::query();

        if ($searchType === 'ten_xe') {
            $query->where('ten_xe', 'like', '%' . $keyword . '%');
        } elseif ($searchType === 'loai_xe') {
            $query->where('loai_xe', 'like', '%' . $keyword . '%');
        } else {
            $query->where(function ($q) use ($keyword) {
                $q->where('ten_xe', 'like', '%' . $keyword . '%')
                  ->orWhere('loai_xe', 'like', '%' . $keyword . '%');
            });
        }

        $xe = $query->orderBy('ten_xe')->get();

        return response()->json([
            'success' => true,
            'data' => $xe,
            'total' => count($xe),
        ]);
    }

    /**
     * Display the specified vehicle
     */
    public function show($ma_xe): JsonResponse
    {
        $xe = Xe::find($ma_xe);

        if (!$xe) {
            return response()->json([
                'success' => false,
                'message' => 'Xe không tồn tại',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $xe,
        ]);
    }

    /**
     * Store a newly created vehicle (admin only)
     */
    public function store(StoreXeRequest $request): JsonResponse
    {
        try {
            $xe = Xe::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Thêm xe thành công',
                'data' => $xe,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm xe: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified vehicle (admin only)
     */
    public function update(UpdateXeRequest $request, $ma_xe): JsonResponse
    {
        $xe = Xe::find($ma_xe);

        if (!$xe) {
            return response()->json([
                'success' => false,
                'message' => 'Xe không tồn tại',
            ], 404);
        }

        try {
            $xe->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật xe thành công',
                'data' => $xe,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật xe: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change the status of a vehicle (admin only)
     */
    public function changeStatus($ma_xe): JsonResponse
    {
        $xe = Xe::find($ma_xe);

        if (!$xe) {
            return response()->json([
                'success' => false,
                'message' => 'Xe không tồn tại',
            ], 404);
        }

        try {
            $xe->trang_thai = $xe->trang_thai == 1 ? 0 : 1;
            $xe->save();

            return response()->json([
                'success' => true,
                'message' => 'Đổi trạng thái xe thành công',
                'data' => $xe,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đổi trạng thái: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete the specified vehicle (admin only)
     */
    public function destroy($ma_xe): JsonResponse
    {
        $xe = Xe::find($ma_xe);

        if (!$xe) {
            return response()->json([
                'success' => false,
                'message' => 'Xe không tồn tại',
            ], 404);
        }

        try {
            $xe->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xoá xe thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xoá xe: ' . $e->getMessage(),
            ], 500);
        }
    }
}
