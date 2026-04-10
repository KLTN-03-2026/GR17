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
        $ke_hoach = KeHoach::with(['nhom', 'hoatDongChiTiets.diaDiem.dichVuDiaDiems'])->find($ma_ke_hoach);

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
            $ke_hoach = KeHoach::create($request->validated());

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
            $ke_hoach->update($request->validated());

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

    /**
     * Gợi ý địa điểm lân cận và cùng tỉnh
     */
    public function suggestNearby(Request $request, $ma_ke_hoach): JsonResponse
    {
        $radius = $request->input('radius', 10); // Bán kính mặc định 10km

        // Lấy chi tiết kế hoạch
        $ke_hoach = KeHoach::with(['hoatDongChiTiets.diaDiem'])->find($ma_ke_hoach);

        if (!$ke_hoach) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại',
            ], 404);
        }

        $diaDiemsInPlan = collect();
        $provinces = collect();

        // Thu thập các địa điểm và tỉnh thành đã có trong kế hoạch
        foreach ($ke_hoach->hoatDongChiTiets as $hoatDong) {
            if ($hoatDong->diaDiem) {
                $diaDiemsInPlan->push($hoatDong->diaDiem);
                
                // Trích xuất tên tỉnh từ địa chỉ (giả định định dạng "... , Tên Tỉnh/Thành phố")
                $addressParts = explode(',', $hoatDong->diaDiem->dia_chi);
                $province = trim(end($addressParts));
                if ($province) {
                    $provinces->push($province);
                }
            }
        }

        if ($diaDiemsInPlan->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch chưa có địa điểm nào để làm gốc gợi ý',
            ], 400);
        }

        $diaDiemsInPlanIds = $diaDiemsInPlan->pluck('ma_dia_diem')->toArray();
        $provinces = $provinces->unique()->toArray();

        // Lấy tất cả các địa điểm khác CHƯA có trong kế hoạch
        $allOtherLocations = \App\Models\DiaDiem::whereNotIn('ma_dia_diem', $diaDiemsInPlanIds)
            ->with(['dichVuDiaDiems', 'tags'])
            ->get();

        $suggestedLocations = collect();

        foreach ($allOtherLocations as $location) {
            $isNearby = false;
            $inSameProvince = false;
            $minDistance = null;

            // 1. Kiểm tra cùng tỉnh
            foreach ($provinces as $prov) {
                if (stripos($location->dia_chi, $prov) !== false) {
                    $inSameProvince = true;
                    break;
                }
            }

            // 2. Tính khoảng cách tới các địa điểm trong kế hoạch (Haversine)
            foreach ($diaDiemsInPlan as $planLocation) {
                $distance = $this->calculateDistance(
                    $planLocation->vi_do, $planLocation->kinh_do,
                    $location->vi_do, $location->kinh_do
                );
                
                if (is_null($minDistance) || $distance < $minDistance) {
                    $minDistance = $distance;
                }

                if ($distance <= $radius) {
                    $isNearby = true;
                }
            }

            // Nếu thuộc bán kính hoặc cùng tỉnh thì thêm vào danh sách gợi ý
            if ($isNearby || $inSameProvince) {
                $location->distance_km = round($minDistance, 2);
                $location->reason = $isNearby ? "Trong bán kính {$radius}km" : "Cùng khu vực/tỉnh thành";
                
                $suggestedLocations->push($location);
            }
        }

        // Sắp xếp theo khoảng cách gần nhất
        $suggestedLocations = $suggestedLocations->sortBy('distance_km')->values();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách địa điểm gợi ý lân cận thành công',
            'radius_applied' => $radius,
            'total' => count($suggestedLocations),
            'data' => $suggestedLocations,
        ]);
    }

    /**
     * Tính khoảng cách Haversine giữa 2 tọa độ (kilometers)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Bán kính Trái Đất (km)

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
