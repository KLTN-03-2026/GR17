<?php

namespace App\Http\Controllers;

use App\Models\ChucVu;
use App\Http\Requests\StoreChucVuRequest;
use App\Http\Requests\UpdateChucVuRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChucVuController extends Controller
{
    /**
     * Display a listing of the active positions (public access)
     */
    public function index(): JsonResponse
    {
        $chucVu = ChucVu::where('tinh_trang', 1)
            ->orderBy('ten_chuc_vu')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $chucVu,
        ]);
    }

    /**
     * Display all positions with pagination (admin only)
     */
    public function indexAll(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $chucVu = ChucVu::orderBy('ma_chuc_vu', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $chucVu->items(),
            'pagination' => [
                'current_page' => $chucVu->currentPage(),
                'per_page' => $chucVu->perPage(),
                'total' => $chucVu->total(),
                'last_page' => $chucVu->lastPage(),
            ],
        ]);
    }

    /**
     * Search positions by name (admin only)
     */
    public function search(Request $request): JsonResponse
    {
        $keyword = $request->query('keyword');

        if (!$keyword || strlen($keyword) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Từ khóa tìm kiếm không được để trống',
            ], 400);
        }

        $chucVu = ChucVu::where('ten_chuc_vu', 'like', '%' . $keyword . '%')
            ->orderBy('ten_chuc_vu')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $chucVu,
            'total' => count($chucVu),
        ]);
    }

    /**
     * Display the specified position
     */
    public function show($ma_chuc_vu): JsonResponse
    {
        $chucVu = ChucVu::find($ma_chuc_vu);

        if (!$chucVu) {
            return response()->json([
                'success' => false,
                'message' => 'Chức vụ không tồn tại',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $chucVu,
        ]);
    }

    /**
     * Store a newly created position (admin only)
     */
    public function store(StoreChucVuRequest $request): JsonResponse
    {
        try {
            $chucVu = ChucVu::create([
                'ten_chuc_vu' => $request->ten_chuc_vu,
                'tinh_trang' => 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm chức vụ thành công',
                'data' => $chucVu,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm chức vụ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified position (admin only)
     */
    public function update(UpdateChucVuRequest $request, $ma_chuc_vu): JsonResponse
    {
        $chucVu = ChucVu::find($ma_chuc_vu);

        if (!$chucVu) {
            return response()->json([
                'success' => false,
                'message' => 'Chức vụ không tồn tại',
            ], 404);
        }

        try {
            if ($request->has('ten_chuc_vu')) {
                $chucVu->ten_chuc_vu = $request->ten_chuc_vu;
            }

            if ($request->has('tinh_trang')) {
                $chucVu->tinh_trang = $request->tinh_trang;
            }

            $chucVu->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật chức vụ thành công',
                'data' => $chucVu,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật chức vụ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change the status of a position (admin only)
     */
    public function changeStatus($ma_chuc_vu): JsonResponse
    {
        $chucVu = ChucVu::find($ma_chuc_vu);

        if (!$chucVu) {
            return response()->json([
                'success' => false,
                'message' => 'Chức vụ không tồn tại',
            ], 404);
        }

        try {
            $chucVu->tinh_trang = $chucVu->tinh_trang == 1 ? 0 : 1;
            $chucVu->save();

            return response()->json([
                'success' => true,
                'message' => 'Đổi tình trạng chức vụ thành công',
                'data' => $chucVu,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đổi tình trạng: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete the specified position (admin only)
     */
    public function destroy($ma_chuc_vu): JsonResponse
    {
        $chucVu = ChucVu::find($ma_chuc_vu);

        if (!$chucVu) {
            return response()->json([
                'success' => false,
                'message' => 'Chức vụ không tồn tại',
            ], 404);
        }

        try {
            $chucVu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xoá chức vụ thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xoá chức vụ: ' . $e->getMessage(),
            ], 500);
        }
    }
}
