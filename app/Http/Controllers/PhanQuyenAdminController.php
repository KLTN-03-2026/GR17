<?php

namespace App\Http\Controllers;

use App\Models\PhanQuyenAdmin;
use App\Models\ChucNang;
use App\Models\ChucVu;
use App\Http\Requests\StorePhanQuyenAdminRequest;
use App\Http\Requests\UpdatePhanQuyenAdminRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PhanQuyenAdminController extends Controller
{
    /**
     * Display a listing of permissions (public access)
     */
    public function index(): JsonResponse
    {
        $phanQuyen = PhanQuyenAdmin::with(['chucNang', 'chucVu'])
            ->orderBy('id_phan_quyen')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $phanQuyen,
            'total' => count($phanQuyen),
        ]);
    }

    /**
     * Display all permissions with pagination (admin only)
     */
    public function indexAll(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $phanQuyen = PhanQuyenAdmin::with(['chucNang', 'chucVu'])
            ->orderBy('id_phan_quyen', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $phanQuyen->items(),
            'pagination' => [
                'current_page' => $phanQuyen->currentPage(),
                'per_page' => $phanQuyen->perPage(),
                'total' => $phanQuyen->total(),
                'last_page' => $phanQuyen->lastPage(),
            ],
        ]);
    }

    /**
     * Search permissions by ChucVu or ChucNang (admin only)
     */
    public function search(Request $request): JsonResponse
    {
        $searchType = $request->query('type', 'all'); // all, chuc_vu, chuc_nang
        $keyword = $request->query('keyword');

        if (!$keyword || strlen($keyword) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Từ khóa tìm kiếm không được để trống',
            ], 400);
        }

        $query = PhanQuyenAdmin::with(['chucNang', 'chucVu']);

        if ($searchType === 'chuc_vu') {
            $query->whereHas('chucVu', function ($q) use ($keyword) {
                $q->where('ten_chuc_vu', 'like', '%' . $keyword . '%');
            });
        } elseif ($searchType === 'chuc_nang') {
            $query->whereHas('chucNang', function ($q) use ($keyword) {
                $q->where('ten_chuc_nang', 'like', '%' . $keyword . '%');
            });
        } else {
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('chucVu', function ($subQ) use ($keyword) {
                    $subQ->where('ten_chuc_vu', 'like', '%' . $keyword . '%');
                })
                ->orWhereHas('chucNang', function ($subQ) use ($keyword) {
                    $subQ->where('ten_chuc_nang', 'like', '%' . $keyword . '%');
                });
            });
        }

        $phanQuyen = $query->get();

        return response()->json([
            'success' => true,
            'data' => $phanQuyen,
            'total' => count($phanQuyen),
        ]);
    }

    /**
     * Display the specified permission
     */
    public function show($id_phan_quyen): JsonResponse
    {
        $phanQuyen = PhanQuyenAdmin::with(['chucNang', 'chucVu'])->find($id_phan_quyen);

        if (!$phanQuyen) {
            return response()->json([
                'success' => false,
                'message' => 'Phân quyền không tồn tại',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $phanQuyen,
        ]);
    }

    /**
     * Store a newly created permission (admin only)
     */
    public function store(StorePhanQuyenAdminRequest $request): JsonResponse
    {
        try {
            // Check if combination already exists
            $existing = PhanQuyenAdmin::where('id_chuc_nang', $request->id_chuc_nang)
                ->where('id_chuc_vu', $request->id_chuc_vu)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phân quyền này đã tồn tại',
                ], 409);
            }

            $phanQuyen = PhanQuyenAdmin::create([
                'id_chuc_nang' => $request->id_chuc_nang,
                'id_chuc_vu' => $request->id_chuc_vu,
            ]);

            $phanQuyen = $phanQuyen->load(['chucNang', 'chucVu']);

            return response()->json([
                'success' => true,
                'message' => 'Thêm phân quyền thành công',
                'data' => $phanQuyen,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm phân quyền: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified permission (admin only)
     */
    public function update(UpdatePhanQuyenAdminRequest $request, $id_phan_quyen): JsonResponse
    {
        $phanQuyen = PhanQuyenAdmin::find($id_phan_quyen);

        if (!$phanQuyen) {
            return response()->json([
                'success' => false,
                'message' => 'Phân quyền không tồn tại',
            ], 404);
        }

        try {
            if ($request->has('id_chuc_nang')) {
                $phanQuyen->id_chuc_nang = $request->id_chuc_nang;
            }

            if ($request->has('id_chuc_vu')) {
                $phanQuyen->id_chuc_vu = $request->id_chuc_vu;
            }

            $phanQuyen->save();
            $phanQuyen = $phanQuyen->load(['chucNang', 'chucVu']);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật phân quyền thành công',
                'data' => $phanQuyen,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật phân quyền: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete the specified permission (admin only)
     */
    public function destroy($id_phan_quyen): JsonResponse
    {
        $phanQuyen = PhanQuyenAdmin::find($id_phan_quyen);

        if (!$phanQuyen) {
            return response()->json([
                'success' => false,
                'message' => 'Phân quyền không tồn tại',
            ], 404);
        }

        try {
            $phanQuyen->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xoá phân quyền thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xoá phân quyền: ' . $e->getMessage(),
            ], 500);
        }
    }
}
