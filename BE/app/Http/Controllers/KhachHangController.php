<?php

namespace App\Http\Controllers;

use App\Http\Requests\KhachHangChangePasswordRequest;
use App\Http\Requests\KhachHangLoginRequest;
use App\Http\Requests\KhachHangRegisterRequest;
use App\Http\Requests\KhachHangStoreByAdminRequest;
use App\Http\Requests\KhachHangUpdateProfileRequest;
use App\Models\Admin;
use App\Models\KhachHang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KhachHangController extends Controller
{
    public function checkLogin(Request $request): JsonResponse
    {
        $khachHang = $request->user();

        if (!$khachHang instanceof KhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập khách hàng',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Khách hàng đã đăng nhập',
            'data' => $khachHang,
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $khachHang = $request->user();
        if ($khachHang) {
            $khachHang->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công',
        ], 200);
    }

    public function register(KhachHangRegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['Mat_khau'] = Hash::make($validated['Mat_khau']);
        $validated['Ngay_sinh'] = date('Y-m-d', strtotime(str_replace('/', '-', $validated['Ngay_sinh'])));

        $khachHang = KhachHang::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công',
            'data' => $khachHang,
        ], 201);
    }

    public function login(KhachHangLoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $khachHang = KhachHang::where('Email', $credentials['Email'])->first();

        if (!$khachHang || !Hash::check($credentials['Mat_khau'], $khachHang->Mat_khau)) {
            return response()->json(['message' => 'Thông tin đăng nhập không chính xác'], 401);
        }

        if (!$khachHang->is_block) {
            return response()->json(['message' => 'Tài khoản của bạn đã bị khóa'], 403);
        }

        $token = $khachHang->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'user' => $khachHang,
        ], 200);
    }

    public function profile(Request $request, ?string $maKhachHang = null): JsonResponse
    {
        $khachHang = $this->resolveAuthorizedCustomer($request, $maKhachHang, true);
        if ($khachHang instanceof JsonResponse) {
            return $khachHang;
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin thành công',
            'data' => $khachHang,
        ], 200);
    }

    public function updateProfile(KhachHangUpdateProfileRequest $request, string $maKhachHang): JsonResponse
    {
        $khachHang = $this->resolveAuthorizedCustomer($request, $maKhachHang, true);
        if ($khachHang instanceof JsonResponse) {
            return $khachHang;
        }

        $validated = $request->validated();
        if (isset($validated['Ngay_sinh'])) {
            $validated['Ngay_sinh'] = date('Y-m-d', strtotime(str_replace('/', '-', $validated['Ngay_sinh'])));
        }

        $khachHang->update($validated);

        return response()->json([
            'message' => 'Cập nhật thông tin thành công',
            'data' => $khachHang->fresh(),
        ]);
    }

    public function changePassword(KhachHangChangePasswordRequest $request, string $maKhachHang): JsonResponse
    {
        $khachHang = $this->resolveAuthorizedCustomer($request, $maKhachHang, false);
        if ($khachHang instanceof JsonResponse) {
            return $khachHang;
        }

        $validated = $request->validated();
        if (!Hash::check($validated['current_password'], $khachHang->Mat_khau)) {
            return response()->json(['message' => 'Mật khẩu hiện tại không chính xác'], 401);
        }

        $khachHang->update(['Mat_khau' => Hash::make($validated['new_password'])]);

        return response()->json(['message' => 'Đổi mật khẩu thành công']);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user instanceof KhachHang && !$user instanceof Admin) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập danh sách khách hàng',
            ], 403);
        }

        $khachHang = KhachHang::all();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách khách hàng thành công',
            'data' => $khachHang,
        ], 200);
    }

    public function storeByAdmin(KhachHangStoreByAdminRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['Mat_khau'] = Hash::make($validated['Mat_khau']);
        $validated['Ngay_sinh'] = date('Y-m-d', strtotime(str_replace('/', '-', $validated['Ngay_sinh'])));

        if ($request->has('is_block')) {
            $validated['is_block'] = $request->is_block;
        }

        $khachHang = KhachHang::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Thêm khách hàng thành công',
            'data' => $khachHang,
        ], 201);
    }

    public function destroy(string $maKhachHang): JsonResponse
    {
        $khachHang = KhachHang::find($maKhachHang);
        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Khách hàng không tồn tại',
            ], 404);
        }

        $khachHang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa khách hàng thành công',
        ], 200);
    }

    private function resolveAuthorizedCustomer(Request $request, ?string $maKhachHang, bool $allowAdmin): KhachHang|JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập',
            ], 401);
        }

        if ($maKhachHang === null) {
            if ($user instanceof KhachHang) {
                $maKhachHang = $user->Ma_khach_hang;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiếu mã khách hàng cần truy cập',
                ], 422);
            }
        }

        $khachHang = KhachHang::where('Ma_khach_hang', $maKhachHang)->first();
        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Khách hàng không tồn tại',
            ], 404);
        }

        if ($allowAdmin && $user instanceof Admin) {
            return $khachHang;
        }

        if (!$user instanceof KhachHang || $user->Ma_khach_hang !== $maKhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập tài nguyên này',
            ], 403);
        }

        return $khachHang;
    }
}
