<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoiTacLoginRequest;
use App\Models\DoiTac;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoiTacAuthController extends Controller
{
    public function login(DoiTacLoginRequest $request)
    {
        $doiTac = DoiTac::where('email', $request->email)->first();

        if (!$doiTac || !Hash::check($request->mat_khau, $doiTac->mat_khau)) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng',
            ], 401);
        }

        if ($doiTac->is_block) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản đối tác đang bị khóa',
            ], 403);
        }

        if ($doiTac->trang_thai_duyet === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn đang chờ Admin duyệt',
            ], 403);
        }

        if ($doiTac->trang_thai_duyet === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn đã bị từ chối: ' . $doiTac->ly_do_tu_choi,
            ], 403);
        }

        $token = $doiTac->createToken('DoiTacToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'doi_tac' => $doiTac,
                'token' => $token,
            ],
        ], 200);
    }

    public function checkLogin(Request $request)
    {
        $doiTac = $request->user();

        if (!$doiTac instanceof DoiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đối tác đã đăng nhập',
            'data' => $doiTac,
        ], 200);
    }

    public function logout(Request $request)
    {
        $doiTac = $request->user();
        if ($doiTac) {
            $doiTac->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công',
        ], 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'ten_doi_tac' => 'required|string|max:255',
            'ten_nguoi_dai_dien' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:doi_tac',
            'mat_khau' => 'required|string|min:6',
            'so_dien_thoai' => 'nullable|string|max:20',
            'dia_chi' => 'nullable|string',
        ]);

        $doiTac = DoiTac::create([
            'ma_doi_tac' => 'DT' . strtoupper(\Illuminate\Support\Str::random(8)),
            'ten_doi_tac' => $request->ten_doi_tac,
            'ten_nguoi_dai_dien' => $request->ten_nguoi_dai_dien,
            'email' => $request->email,
            'mat_khau' => Hash::make($request->mat_khau),
            'so_dien_thoai' => $request->so_dien_thoai,
            'dia_chi' => $request->dia_chi,
            'is_block' => false,
            'trang_thai_duyet' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công, vui lòng chờ Admin kiểm duyệt',
            'data' => $doiTac,
        ], 201);
    }

    public function updateProfile(Request $request)
    {
        $doiTac = $request->user();

        if (!$doiTac instanceof DoiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $validated = $request->validate([
            'ten_doi_tac' => 'required|string|max:255',
            'ten_nguoi_dai_dien' => 'required|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
            'dia_chi' => 'nullable|string',
            'ma_so_thue' => 'nullable|string|max:50',
        ]);

        $doiTac->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công',
            'data' => $doiTac->fresh(),
        ]);
    }

    public function changePassword(Request $request)
    {
        $doiTac = $request->user();

        if (!$doiTac instanceof DoiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        if (!Hash::check($request->current_password, $doiTac->mat_khau)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không chính xác'
            ], 400);
        }

        $doiTac->update([
            'mat_khau' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }
}
