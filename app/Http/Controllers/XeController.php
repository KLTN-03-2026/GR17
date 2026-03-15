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
     * Display all vehicles with pagination (admin only)
     */
    public function indexAll(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $xe = Xe::orderBy('id_xe', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $xe->items(),
            'pagination' => [
                'current_page' => $xe->currentPage(),
                'per_page' => $xe->perPage(),
                'total' => $xe->total(),
                'last_page' => $xe->lastPage(),
            ],
        ]);
    }

    /**
     * Search vehicles by name or type (admin only)
     */
    public function search(Request $request): JsonResponse
    {
        $searchType = $request->query('type', 'all'); // all, ten_xe, loai_xe
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
    public function show($id_xe): JsonResponse
    {
        $xe = Xe::find($id_xe);

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
            $xe = Xe::create([
                'ten_xe' => $request->ten_xe,
                'loai_xe' => $request->loai_xe,
                'so_cho' => $request->so_cho,
                'gia_theo_ngay' => $request->gia_theo_ngay,
                'tong_so_xe' => $request->tong_so_xe,
                'mo_ta' => $request->mo_ta,
                'trang_thai' => 1,
            ]);

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
    public function update(UpdateXeRequest $request, $id_xe): JsonResponse
    {
        $xe = Xe::find($id_xe);

        if (!$xe) {
            return response()->json([
                'success' => false,
                'message' => 'Xe không tồn tại',
            ], 404);
        }

        try {
            if ($request->has('ten_xe')) {
                $xe->ten_xe = $request->ten_xe;
            }

            if ($request->has('loai_xe')) {
                $xe->loai_xe = $request->loai_xe;
            }

            if ($request->has('so_cho')) {
                $xe->so_cho = $request->so_cho;
            }

            if ($request->has('gia_theo_ngay')) {
                $xe->gia_theo_ngay = $request->gia_theo_ngay;
            }

            if ($request->has('tong_so_xe')) {
                $xe->tong_so_xe = $request->tong_so_xe;
            }

            if ($request->has('mo_ta')) {
                $xe->mo_ta = $request->mo_ta;
            }

            if ($request->has('trang_thai')) {
                $xe->trang_thai = $request->trang_thai;
            }

            $xe->save();

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
    public function changeStatus($id_xe): JsonResponse
    {
        $xe = Xe::find($id_xe);

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
    public function destroy($id_xe): JsonResponse
    {
        $xe = Xe::find($id_xe);

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
