<?php

namespace App\Http\Controllers;

use App\Models\DanhSachYeuThich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Requests\StoreDanhSachYeuThichRequest;
use App\Http\Requests\UpdateDanhSachYeuThichRequest;

class DanhSachYeuThichController extends Controller
{
    /**
     * Resolve customer id from auth user, body, or query.
     */
    private function resolveCustomerId(Request $request): ?string
    {
        $user = $request->user();

        $candidate = $user->Ma_khach_hang
            ?? $user->ma_khach_hang
            ?? $user->id
            ?? $request->input('ma_khach_hang')
            ?? $request->query('ma_khach_hang');

        if (!is_scalar($candidate)) {
            return null;
        }

        $value = trim((string) $candidate);
        return $value !== '' ? $value : null;
    }

    /**
     * List favorite destinations for current customer.
     */
    public function indexCustomer(Request $request)
    {
        $maKhachHang = $this->resolveCustomerId($request);

        if (!$maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp mã khách hàng hoặc đăng nhập',
            ], 401);
        }

        $danhSach = DanhSachYeuThich::query()
            ->with('diaDiem')
            ->where('ma_khach_hang', $maKhachHang)
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách yêu thích thành công',
            'data' => $danhSach,
        ], 200);
    }

    /**
     * Add destination to customer favorite list.
     */
    public function storeCustomer(StoreDanhSachYeuThichRequest $request)
    {
        $maKhachHang = $this->resolveCustomerId($request);

        if (!$maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp mã khách hàng hoặc đăng nhập để thêm vào danh sách',
            ], 401);
        }

        $exists = DanhSachYeuThich::query()
            ->where('ma_khach_hang', $maKhachHang)
            ->where('ma_dia_diem', $request->ma_dia_diem)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm này đã có trong danh sách yêu thích của bạn',
            ], 409);
        }

        try {
            $payload = [
                'ma_khach_hang' => $maKhachHang,
                'ma_dia_diem' => (string) $request->ma_dia_diem,
            ];

            if ($request->filled('ma_danh_sach_ua_thich')) {
                $payload['ma_danh_sach_ua_thich'] = (string) $request->ma_danh_sach_ua_thich;
            }

            $danhSach = DanhSachYeuThich::create($payload);
            $danhSach->load('diaDiem');

            return response()->json([
                'success' => true,
                'message' => 'Thêm vào danh sách yêu thích thành công',
                'data' => $danhSach,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm vào danh sách yêu thích',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Update a favorite item for current customer.
     */
    public function updateCustomer(UpdateDanhSachYeuThichRequest $request, $ma_danh_sach_ua_thich)
    {
        $maKhachHang = $this->resolveCustomerId($request);

        if (!$maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp mã khách hàng hoặc đăng nhập',
            ], 401);
        }

        $danhSach = DanhSachYeuThich::query()->find($ma_danh_sach_ua_thich);

        if (!$danhSach) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy mục trong danh sách yêu thích',
            ], 404);
        }

        if ((string) $danhSach->ma_khach_hang !== (string) $maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể cập nhật danh sách yêu thích của người khác',
            ], 403);
        }

        try {
            $danhSach->ma_dia_diem = (string) $request->ma_dia_diem;
            $danhSach->save();
            $danhSach->load('diaDiem');

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật danh sách yêu thích thành công',
                'data' => $danhSach,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật thất bại',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Remove destination from favorite list.
     */
    public function destroyCustomer(Request $request, $ma_danh_sach_ua_thich)
    {
        $maKhachHang = $this->resolveCustomerId($request);

        if (!$maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp mã khách hàng hoặc đăng nhập',
            ], 401);
        }

        $danhSach = DanhSachYeuThich::query()->find($ma_danh_sach_ua_thich);

        if (!$danhSach) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy mục trong danh sách yêu thích',
            ], 404);
        }

        if ((string) $danhSach->ma_khach_hang !== (string) $maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể xóa danh sách yêu thích của người khác',
            ], 403);
        }

        try {
            $danhSach->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa khỏi danh sách yêu thích thành công',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa thất bại',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
