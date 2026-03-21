<?php

namespace App\Http\Controllers;

use App\Models\PhanQuyenAdmin;
use App\Http\Requests\StorePhanQuyenAdminRequest;
use App\Http\Requests\UpdatePhanQuyenAdminRequest;
use Illuminate\Http\Request;

class PhanQuyenAdminController extends Controller
{
    public function index()
    {
        $phanQuyen = PhanQuyenAdmin::with(['chucNang', 'chucVu'])
            ->orderBy('ma_phan_quyen')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $phanQuyen,
            'total' => $phanQuyen->count(),
        ]);
    }

    public function indexAll()
    {
        $phanQuyen = PhanQuyenAdmin::with(['chucNang', 'chucVu'])
            ->orderBy('ma_phan_quyen', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $phanQuyen,
            'total' => $phanQuyen->count(),
        ]);
    }

    // public function search(Request $request)
    // {
    //     $searchType = $request->query('type', 'all');
    //     $keyword = $request->query('keyword');

    //     if (!$keyword) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Từ khóa tìm kiếm không được để trống',
    //         ], 400);
    //     }

    //     $query = PhanQuyenAdmin::with(['chucNang', 'chucVu']);

    //     if ($searchType === 'chuc_vu') {
    //         $query->whereHas('chucVu', function ($q) use ($keyword) {
    //             $q->where('ten_chuc_vu', 'like', "%{$keyword}%");
    //         });
    //     }
    //     elseif ($searchType === 'chuc_nang') {
    //         $query->whereHas('chucNang', function ($q) use ($keyword) {
    //             $q->where('ten_chuc_nang', 'like', "%{$keyword}%");
    //         });
    //     }
    //     else {
    //         $query->where(function ($q) use ($keyword) {
    //             $q->whereHas('chucVu', function ($subQ) use ($keyword) {
    //                     $subQ->where('ten_chuc_vu', 'like', "%{$keyword}%");
    //                 }
    //                 )
    //                     ->orWhereHas('chucNang', function ($subQ) use ($keyword) {
    //                 $subQ->where('ten_chuc_nang', 'like', "%{$keyword}%");
    //             }
    //             );
    //         });
    //     }

    //     $phanQuyen = $query->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $phanQuyen,
    //         'total' => $phanQuyen->count(),
    //     ]);
    // }

    public function show($ma_phan_quyen)
    {
        $phanQuyen = PhanQuyenAdmin::with(['chucNang', 'chucVu'])
            ->find($ma_phan_quyen);

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

    public function store(StorePhanQuyenAdminRequest $request)
    {
        try {
            $existing = PhanQuyenAdmin::where('ma_chuc_nang', $request->ma_chuc_nang)
                ->where('ma_chuc_vu', $request->ma_chuc_vu)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phân quyền này đã tồn tại',
                ], 409);
            }

            $phanQuyen = PhanQuyenAdmin::create($request->validated());
            $phanQuyen = $phanQuyen->load(['chucNang', 'chucVu']);

            return response()->json([
                'success' => true,
                'message' => 'Thêm phân quyền thành công',
                'data' => $phanQuyen,
            ], 201);

        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm phân quyền: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdatePhanQuyenAdminRequest $request, $ma_phan_quyen)
    {
        $phanQuyen = PhanQuyenAdmin::find($ma_phan_quyen);

        if (!$phanQuyen) {
            return response()->json([
                'success' => false,
                'message' => 'Phân quyền không tồn tại',
            ], 404);
        }

        try {
            $phanQuyen->update($request->validated());
            $phanQuyen = $phanQuyen->load(['chucNang', 'chucVu']);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật phân quyền thành công',
                'data' => $phanQuyen,
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật phân quyền: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($ma_phan_quyen)
    {
        $phanQuyen = PhanQuyenAdmin::find($ma_phan_quyen);

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

        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xoá phân quyền: ' . $e->getMessage(),
            ], 500);
        }
    }
}