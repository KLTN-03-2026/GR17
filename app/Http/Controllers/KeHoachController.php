<?php

namespace App\Http\Controllers;

use App\Models\KeHoach;
use App\Models\ThanhVienNhom;
use App\Http\Requests\StoreKeHoachRequest;
use App\Http\Requests\UpdateKeHoachRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KeHoachController extends Controller
{
    /**
     * Display a listing of active plans with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $khachHang = $request->user();
        $maKhachHang = $request->ma_khach_hang ?? ($khachHang ? $khachHang->Ma_khach_hang : null);

        if (!$maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp mã khách hàng hoặc đăng nhập',
            ], 401);
        }

        $maNhoms = ThanhVienNhom::where('Ma_khach_hang', $maKhachHang)->pluck('Ma_nhom');
        $ke_hoach = KeHoach::whereIn('ma_nhom', $maNhoms)
            ->with('nhom')
            ->orderBy('ma_ke_hoach', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $ke_hoach,
        ]);
    }

    /**
     * Display all plans (admin view)
     */
    public function indexAll(): JsonResponse
    {
        $ke_hoach = KeHoach::with('nhom')->orderBy('ma_ke_hoach', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $ke_hoach,
            'total' => count($ke_hoach),
        ]);
    }

    /**
     * Search plans by various criteria
     */
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

        $query = KeHoach::query();

        if ($searchType === 'ten_ke_hoach') {
            $query->where('ten_ke_hoach', 'like', '%' . $keyword . '%');
        } elseif ($searchType === 'ma_nhom') {
            $query->where('ma_nhom', 'like', '%' . $keyword . '%');
        } else {
            $query->where(function ($q) use ($keyword) {
                $q->where('ten_ke_hoach', 'like', '%' . $keyword . '%')
                  ->orWhere('ma_nhom', 'like', '%' . $keyword . '%');
            });
        }

        $ke_hoach = $query->with('nhom')->orderBy('ten_ke_hoach')->get();

        return response()->json([
            'success' => true,
            'data' => $ke_hoach,
            'total' => count($ke_hoach),
        ]);
    }

    /**
     * Display the specified plan
     */
    public function show($ma_ke_hoach): JsonResponse
    {
        $ke_hoach = KeHoach::with('nhom')->find($ma_ke_hoach);

        if (!$ke_hoach) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ke_hoach,
        ]);
    }

    /**
     * Store a newly created plan
     */
    public function store(StoreKeHoachRequest $request): JsonResponse
    {
        $maKhachHang = $request->input('ma_khach_hang');
        if ($maKhachHang) {
            $isMember = ThanhVienNhom::where('Ma_khach_hang', $maKhachHang)
                ->where('Ma_nhom', $request->ma_nhom)
                ->exists();

            if (!$isMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khách hàng không thuộc nhóm hành trình đã chọn',
                ], 422);
            }
        }

        try {
            $ke_hoach = KeHoach::create([
                'ma_ke_hoach' => $request->ma_ke_hoach,
                'ma_nhom' => $request->ma_nhom,
                'ten_ke_hoach' => $request->ten_ke_hoach,
                'so_nguoi' => $request->so_nguoi,
                'ngay_bat_dau' => $request->ngay_bat_dau,
                'ngay_ket_thuc' => $request->ngay_ket_thuc,
                'ngan_sach_du_kien' => $request->ngan_sach_du_kien,
                'trang_thai' => $request->trang_thai,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm kế hoạch thành công',
                'data' => $ke_hoach->load('nhom'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm kế hoạch: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified plan
     */
    public function update(UpdateKeHoachRequest $request, $ma_ke_hoach): JsonResponse
    {
        $ke_hoach = KeHoach::find($ma_ke_hoach);

        if (!$ke_hoach) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại',
            ], 404);
        }

        $maKhachHang = $request->input('ma_khach_hang');
        if ($maKhachHang) {
            $targetGroup = $request->input('ma_nhom', $ke_hoach->ma_nhom);
            $isMember = ThanhVienNhom::where('Ma_khach_hang', $maKhachHang)
                ->where('Ma_nhom', $targetGroup)
                ->exists();

            if (!$isMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khách hàng không thuộc nhóm hành trình đã chọn',
                ], 422);
            }
        }

        try {
            if ($request->has('ma_nhom')) {
                $ke_hoach->ma_nhom = $request->ma_nhom;
            }

            if ($request->has('ten_ke_hoach')) {
                $ke_hoach->ten_ke_hoach = $request->ten_ke_hoach;
            }

            if ($request->has('so_nguoi')) {
                $ke_hoach->so_nguoi = $request->so_nguoi;
            }

            if ($request->has('ngay_bat_dau')) {
                $ke_hoach->ngay_bat_dau = $request->ngay_bat_dau;
            }

            if ($request->has('ngay_ket_thuc')) {
                $ke_hoach->ngay_ket_thuc = $request->ngay_ket_thuc;
            }

            if ($request->has('ngan_sach_du_kien')) {
                $ke_hoach->ngan_sach_du_kien = $request->ngan_sach_du_kien;
            }

            if ($request->has('trang_thai')) {
                $ke_hoach->trang_thai = $request->trang_thai;
            }

            $ke_hoach->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật kế hoạch thành công',
                'data' => $ke_hoach->load('nhom'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật kế hoạch: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change the status of a plan
     */
    public function changeStatus($ma_ke_hoach): JsonResponse
    {
        $ke_hoach = KeHoach::find($ma_ke_hoach);

        if (!$ke_hoach) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại',
            ], 404);
        }

        try {
            $ke_hoach->trang_thai = $ke_hoach->trang_thai == 1 ? 0 : 1;
            $ke_hoach->save();

            return response()->json([
                'success' => true,
                'message' => 'Đổi trạng thái kế hoạch thành công',
                'data' => $ke_hoach,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đổi trạng thái: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete the specified plan
     */
    public function destroy($ma_ke_hoach): JsonResponse
    {
        $ke_hoach = KeHoach::find($ma_ke_hoach);

        if (!$ke_hoach) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại',
            ], 404);
        }

        try {
            $ke_hoach->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xoá kế hoạch thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xoá kế hoạch: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get plans by group (ma_nhom)
     */
    public function getByGroup($ma_nhom): JsonResponse
    {
        $ke_hoach = KeHoach::where('ma_nhom', $ma_nhom)
            ->where('trang_thai', 1)
            ->with('nhom')
            ->get();

        if ($ke_hoach->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kế hoạch cho nhóm này',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ke_hoach,
            'total' => count($ke_hoach),
        ]);
    }
}
