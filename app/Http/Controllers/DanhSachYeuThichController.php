<?php

namespace App\Http\Controllers;

use App\Models\DanhSachYeuThich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
                'message' => 'Vui long cung cap ma khach hang hoac dang nhap',
            ], 401);
        }

        $danhSach = DanhSachYeuThich::query()
            ->with('diaDiem')
            ->where('ma_khach_hang', $maKhachHang)
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lay danh sach yeu thich thanh cong',
            'data' => $danhSach,
        ], 200);
    }

    /**
     * Add destination to customer favorite list.
     */
    public function storeCustomer(Request $request)
    {
        $maKhachHang = $this->resolveCustomerId($request);

        if (!$maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui long cung cap ma khach hang hoac dang nhap de them vao danh sach',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'ma_dia_diem' => 'required|exists:dia_diem,ma_dia_diem',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Du lieu khong hop le',
                'errors' => $validator->errors(),
            ], 422);
        }

        $exists = DanhSachYeuThich::query()
            ->where('ma_khach_hang', $maKhachHang)
            ->where('ma_dia_diem', $request->ma_dia_diem)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Dia diem nay da co trong danh sach yeu thich cua ban',
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
                'message' => 'Them vao danh sach yeu thich thanh cong',
                'data' => $danhSach,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Co loi xay ra khi them vao danh sach yeu thich',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Update a favorite item for current customer.
     */
    public function updateCustomer(Request $request, $ma_danh_sach_ua_thich)
    {
        $maKhachHang = $this->resolveCustomerId($request);

        if (!$maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Vui long cung cap ma khach hang hoac dang nhap',
            ], 401);
        }

        $danhSach = DanhSachYeuThich::query()->find($ma_danh_sach_ua_thich);

        if (!$danhSach) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay muc trong danh sach yeu thich',
            ], 404);
        }

        if ((string) $danhSach->ma_khach_hang !== (string) $maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Ban khong the cap nhat danh sach yeu thich cua nguoi khac',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'ma_dia_diem' => [
                'required',
                'exists:dia_diem,ma_dia_diem',
                Rule::unique('danh_sach_yeu_thich', 'ma_dia_diem')
                    ->where(fn ($query) => $query->where('ma_khach_hang', $maKhachHang))
                    ->ignore($danhSach->ma_danh_sach_ua_thich, 'ma_danh_sach_ua_thich'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Du lieu khong hop le',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $danhSach->ma_dia_diem = (string) $request->ma_dia_diem;
            $danhSach->save();
            $danhSach->load('diaDiem');

            return response()->json([
                'success' => true,
                'message' => 'Cap nhat danh sach yeu thich thanh cong',
                'data' => $danhSach,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cap nhat that bai',
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
                'message' => 'Vui long cung cap ma khach hang hoac dang nhap',
            ], 401);
        }

        $danhSach = DanhSachYeuThich::query()->find($ma_danh_sach_ua_thich);

        if (!$danhSach) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay muc trong danh sach yeu thich',
            ], 404);
        }

        if ((string) $danhSach->ma_khach_hang !== (string) $maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Ban khong the xoa danh sach yeu thich cua nguoi khac',
            ], 403);
        }

        try {
            $danhSach->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xoa khoi danh sach yeu thich thanh cong',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xoa that bai',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
