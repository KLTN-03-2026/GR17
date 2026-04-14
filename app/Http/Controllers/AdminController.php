<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AdminStoreRequest;
use App\Http\Requests\AdminChangePasswordRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\Http\Requests\AdminSearchRequest;
use App\Http\Requests\AdminChangeStatusRequest;

class AdminController extends Controller
{
    public function checkLogin(Request $request)
    {
        $admin = $request->user();

        if (!$admin instanceof Admin) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập admin'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Admin đã đăng nhập',
            'data' => $admin
        ], 200);
    }

    public function login(AdminLoginRequest $request)
    {
        $admin = Admin::where('Email', $request->email)->first();
        if (!$admin || !Hash::check($request->Mat_khau, $admin->Mat_khau)) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }
        $token = $admin->createToken('AdminToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'admin' => $admin,
                'token' => $token
            ]
        ], 200);
    }
    public function index()
    {
        $admins = Admin::all();

        if ($admins->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quản trị viên nào'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách quản trị viên thành công',
            'data' => $admins
        ], 200);
    }
    public function show($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy quản trị viên'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin quản trị viên thành công',
            'data' => $admin
        ], 200);
    }
    public function store(AdminStoreRequest $request)
    {
        try {

            $data = $request->validated();
            $data['Mat_khau'] = Hash::make($data['Mat_khau']);

            $admin = Admin::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Thêm quản trị viên thành công',
                'data' => $admin
            ], 201);
        }
        catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm quản trị viên'
            ], 500);
        }
    }
    public function update(AdminUpdateRequest $request, $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy quản trị viên'], 404);
        }
        if ($admin->IsAdmin == 1) {
            return response()->json(['success' => false, 'message' => 'Không được phép cập nhật tài khoản Admin hệ thống'], 403);
        }
        try {
            $admin->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật quản trị viên thành công',
                'data' => $admin
            ], 200);
        }
        catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Cập nhật quản trị viên thất bại'
            ], 500);
        }
    }
    public function delete($id)
    {
        $admin = Admin::find($id);
        // if (!$admin) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Không tìm thấy quản trị viên'
        //     ], 404);
        // }
        if ($admin && $admin->IsAdmin == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Không được phép xóa tài khoản Admin hệ thống'
            ], 403);
        }
        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa quản trị viên thành công'
        ], 200);
    }
    public function search(AdminSearchRequest $request)
    {
        $query = Admin::query();
        $validated = $request->validated();

        if (isset($validated['so_dien_thoai'])) {
            $query->where('so_dien_thoai', $validated['so_dien_thoai']);
        }

        if (isset($validated['email'])) {
            $query->where('Email', $validated['email']);
        }

        if (isset($validated['name'])) {
            $query->where('Ho_va_ten', 'like', '%' . $validated['name'] . '%');
        }

        $admins = $query->get();

        if ($admins->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy quản trị viên'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Tìm kiếm thành công',
            'data' => $admins
        ], 200);
    }
    public function changePassword(AdminChangePasswordRequest $request)
    {
        $admin = $request->user();
        if (!Hash::check($request->current_password, $admin->Mat_khau)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không chính xác'
            ], 403);
        }

        $admin->Mat_khau = Hash::make($request->new_password);
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công'
        ], 200);
    }
    public function changeStatus(AdminChangeStatusRequest $request, $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy quản trị viên'
            ], 404);
        }
        if ($admin->IsAdmin == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Không được phép thay đổi trạng thái tài khoản Admin hệ thống'
            ], 403);
        }
        $admin->update($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $admin
        ], 200);
    }
}
