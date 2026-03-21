<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KhachHangController extends Controller
{
    public function checkLogin(Request $request)
    {
        $khachHang = $request->user();

        if (!$khachHang instanceof KhachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Ban chua dang nhap khach hang'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Khach hang da dang nhap',
            'data' => $khachHang
        ], 200);
    }

    /**
     * ÄÄƒng kÃ½ tÃ i khoáº£n khÃ¡ch hÃ ng.
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
            'Ho_va_ten.regex' => 'Ho va ten chi duoc chua chu cai va khoang trang.',
            'Ngay_sinh.date_format' => 'Ngay sinh phai dung dinh dang dd/mm/YYYY.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Du lieu dang ky khong hop le',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['Mat_khau'] = Hash::make($validated['Mat_khau']);
        $validated['Ngay_sinh'] = date('Y-m-d', strtotime(str_replace('/', '-', $validated['Ngay_sinh'])));

        $khachHang = KhachHang::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dang ky thanh cong',
            'data' => $khachHang
        ], 201);
    }

    /**
     * Dang nhap khach hang.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'Email' => 'required|email',
            'Mat_khau' => 'required|string',
        ]);

        $khachHang = KhachHang::where('Email', $credentials['Email'])->first();

        if (!$khachHang || !Hash::check($credentials['Mat_khau'], $khachHang->Mat_khau)) {
            return response()->json(['message' => 'ThÃ´ng tin Ä‘Äƒng nháº­p khÃ´ng chÃ­nh xÃ¡c'], 401);
        }

        if (!$khachHang->is_block) {
            return response()->json(['message' => 'TÃ i khoáº£n cá»§a báº¡n Ä‘Ã£ bá»‹ khÃ³a'], 403);
        }

        $token = $khachHang->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'ÄÄƒng nháº­p thÃ nh cÃ´ng', 'token' => $token], 200);
    }

    /**
     * Xem thÃ´ng tin cÃ¡ nhÃ¢n dá»±a trÃªn Ma_khach_hang.
     */
    public function profile(Request $request, $maKhachHang)
    {
        $validator = Validator::make(['Ma_khach_hang' => $maKhachHang], [
            'Ma_khach_hang' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Láº¥y thÃ´ng tin cÃ¡ nhÃ¢n tháº¥t báº¡i',
                'errors' => $validator->errors()
            ], 422);
        }

        $khachHang = KhachHang::where('Ma_khach_hang', $maKhachHang)->first();

        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'KhÃ¡ch hÃ ng khÃ´ng tá»“n táº¡i'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Láº¥y thÃ´ng tin thÃ nh cÃ´ng',
            'data' => $khachHang
        ], 200);
    }
    /**
     * Cáº­p nháº­t thÃ´ng tin cÃ¡ nhÃ¢n dá»±a trÃªn Ma_khach_hang.
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

        return response()->json(['message' => 'Cáº­p nháº­t thÃ´ng tin thÃ nh cÃ´ng', 'data' => $khachHang]);
    }

    /**
     * Äá»•i máº­t kháº©u dá»±a trÃªn Ma_khach_hang.
     */
    public function changePassword(Request $request, $maKhachHang)
    {
        $khachHang = KhachHang::findOrFail($maKhachHang);

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        if (!Hash::check($validated['current_password'], $khachHang->Mat_khau)) {
            return response()->json(['message' => 'Máº­t kháº©u hiá»‡n táº¡i khÃ´ng chÃ­nh xÃ¡c'], 401);
        }

        $khachHang->update(['Mat_khau' => Hash::make($validated['new_password'])]);

        return response()->json(['message' => 'Äá»•i máº­t kháº©u thÃ nh cÃ´ng']);
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
            'data' => $khachHang
        ], 200);
    }
}



