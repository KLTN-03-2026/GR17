<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'Mat_khau' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'Ma_admin' => 'required|unique:admins|max:10',
            'Ho_va_ten' => 'required|min:5|max:40',
            'Mat_khau' => 'required|min:8',
            'Email' => 'required|email|unique:admins',
            'Ngay_sinh' => 'required|date_format:Y-m-d',
            'Gioi_tinh' => 'required|boolean',
            'ma_chuc_vu' => 'required',
            'so_dien_thoai' => 'required|unique:admins|regex:/^0[0-9]{9}$/',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Thêm quản trị viên thất bại',
                'errors' => $validator->errors()
            ], 422);
        }
        try {

            $data = $request->all();
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
    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);
        // if (!$admin) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Không tìm thấy quản trị viên'
        //     ], 404);
        // }
        try {

            $admin->update($request->all());

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
        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa quản trị viên thành công'
        ], 200);
    }
    public function search(Request $request)
    {
        $query = Admin::query();

        if ($request->so_dien_thoai) {
            $query->where('so_dien_thoai', $request->so_dien_thoai);
        }

        if ($request->email) {
            $query->where('Email', $request->email);
        }

        if ($request->name) {
            $query->where('Ho_va_ten', 'like', '%' . $request->name . '%');
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
    public function changePassword(Request $request)
    {
        $admin = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }
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
    public function changeStatus(Request $request, $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy quản trị viên'
            ], 404);
        }
        $admin->is_block = $request->is_block;
        $admin->save();
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $admin
        ], 200);
    }
}
