<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\KhachHangRegisterRequest;
use App\Http\Requests\KhachHangLoginRequest;
use App\Http\Requests\KhachHangUpdateProfileRequest;
use App\Http\Requests\KhachHangChangePasswordRequest;
use App\Http\Requests\KhachHangStoreByAdminRequest;

class KhachHangController extends Controller
{
    public function checkLogin(Request $request)
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

    /**
     * Đăng ký tài khoản khách hàng.
     */
    public function register(KhachHangRegisterRequest $request)
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

    /**
     * Đăng nhập khách hàng.
     */
    public function login(KhachHangLoginRequest $request)
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

        return response()->json(['message' => 'Đăng nhập thành công', 'token' => $token], 200);
    }

    /**
     * Xem thông tin cá nhân dựa trên Ma_khach_hang.
     */
    public function profile(Request $request, $maKhachHang)
    {
        $validator = Validator::make(['Ma_khach_hang' => $maKhachHang], [
            'Ma_khach_hang' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lấy thông tin cá nhân thất bại',
                'errors' => $validator->errors(),
            ], 422);
        }

        $khachHang = KhachHang::where('Ma_khach_hang', $maKhachHang)->first();

        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Khách hàng không tồn tại',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin thành công',
            'data' => $khachHang,
        ], 200);
    }

    /**
     * Cập nhật thông tin cá nhân dựa trên Ma_khach_hang.
     */
    public function updateProfile(KhachHangUpdateProfileRequest $request, $maKhachHang)
    {
        $khachHang = KhachHang::findOrFail($maKhachHang);

        $validated = $request->validated();

        if (isset($validated['Ngay_sinh'])) {
            $validated['Ngay_sinh'] = date('Y-m-d', strtotime(str_replace('/', '-', $validated['Ngay_sinh'])));
        }

        $khachHang->update($validated);

        return response()->json(['message' => 'Cập nhật thông tin thành công', 'data' => $khachHang]);
    }

    /**
     * Đổi mật khẩu dựa trên Ma_khach_hang.
     */
    public function changePassword(KhachHangChangePasswordRequest $request, $maKhachHang)
    {
        $khachHang = KhachHang::findOrFail($maKhachHang);

        $validated = $request->validated();

        if (!Hash::check($validated['current_password'], $khachHang->Mat_khau)) {
            return response()->json(['message' => 'Mật khẩu hiện tại không chính xác'], 401);
        }

        $khachHang->update(['Mat_khau' => Hash::make($validated['new_password'])]);

        return response()->json(['message' => 'Đổi mật khẩu thành công']);
    }

    /**
     * Lấy danh sách tất cả khách hàng
     */
    public function index()
    {
        $khachHang = KhachHang::all();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách khách hàng thành công',
            'data' => $khachHang,
        ], 200);
    }

    /**
     * Thêm mới khách hàng (Admin)
     */
    public function storeByAdmin(KhachHangStoreByAdminRequest $request)
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

    /**
     * Xóa khách hàng (Admin)
     */
    public function destroy($maKhachHang)
    {
        $khachHang = KhachHang::find($maKhachHang);
        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Khách hàng không tồn tại'
            ], 404);
        }
        
        $khachHang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa khách hàng thành công'
        ], 200);
    }
}
