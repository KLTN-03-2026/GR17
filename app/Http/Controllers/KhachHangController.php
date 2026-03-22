<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Ho_va_ten' => ['required', 'string', 'min:5', 'max:40', 'regex:/^[\pL\s]+$/u'],
            'Mat_khau' => 'required|string|min:8',
            'Email' => 'required|email|unique:khach_hang,Email',
            'Ngay_sinh' => 'required|date_format:d/m/Y',
            'Gioi_tinh' => 'required|boolean',
            'so_dien_thoai' => 'required|regex:/^0[0-9]{9}$/|unique:khach_hang,so_dien_thoai',
        ], [
            'Ho_va_ten.regex' => 'Họ và tên chỉ được chứa chữ cái và khoảng trắng.',
            'Ngay_sinh.date_format' => 'Ngày sinh phải đúng định dạng dd/mm/YYYY.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu đăng ký không hợp lệ',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
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
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'Email' => 'required|email',
            'Mat_khau' => 'required|string',
        ]);

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
    public function updateProfile(Request $request, $maKhachHang)
    {
        $khachHang = KhachHang::findOrFail($maKhachHang);

        $validated = $request->validate([
            'Ho_va_ten' => 'nullable|string|min:5|max:40|regex:/^[\\pL\\s]+$/u',
            'Ngay_sinh' => 'nullable|date_format:d/m/Y',
            'Gioi_tinh' => 'nullable|boolean',
            'so_dien_thoai' => 'nullable|regex:/^0[0-9]{9}$/|unique:khach_hang,so_dien_thoai,' . $maKhachHang . ',Ma_khach_hang',
        ]);

        if (isset($validated['Ngay_sinh'])) {
            $validated['Ngay_sinh'] = date('Y-m-d', strtotime(str_replace('/', '-', $validated['Ngay_sinh'])));
        }

        $khachHang->update($validated);

        return response()->json(['message' => 'Cập nhật thông tin thành công', 'data' => $khachHang]);
    }

    /**
     * Đổi mật khẩu dựa trên Ma_khach_hang.
     */
    public function changePassword(Request $request, $maKhachHang)
    {
        $khachHang = KhachHang::findOrFail($maKhachHang);

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

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
}
