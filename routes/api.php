<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\DiaDiemController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TagDiaDiemController;
use App\Http\Controllers\NhomController;
use App\Http\Controllers\ThanhVienNhomController;
use App\Http\Controllers\DanhGiaKeHoachController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\ChucNangController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\PhanQuyenAdminController;
use App\Http\Controllers\XeController;
use App\Http\Controllers\XeKeHoachController;
use App\Http\Controllers\DichVuDiaDiemController;
use App\Http\Controllers\KeHoachController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\ChiTietTourController;
use App\Http\Controllers\TourKhoiHanhController;
use App\Http\Controllers\CauHinhNgayController;
use App\Http\Controllers\DanhSachYeuThichController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Public routes
Route::post('/admin/login', [AdminController::class, 'login']);
Route::get('/khach-hang', [KhachHangController::class, 'index']);
Route::post('/khach-hang/register', [KhachHangController::class, 'register']);
Route::post('/khach-hang/login', [KhachHangController::class, 'login']);
Route::middleware('auth:sanctum')->get('/admin/check-login', [AdminController::class, 'checkLogin']);
Route::middleware('auth:sanctum')->get('/khach-hang/check-login', [KhachHangController::class, 'checkLogin']);

// Public routes for DiaDiem
Route::get('/dia-diem', [DiaDiemController::class, 'index']);
Route::get('/dia-diem/{maDiaDiem}', [DiaDiemController::class, 'show']);
Route::get('/dia-diem/filter/type/{loai}', [DiaDiemController::class, 'filterByType']);
Route::get('/dia-diem/filter/tag/{maTag}', [DiaDiemController::class, 'filterByTag']);

// Public routes for Tag
Route::get('/tag', [TagController::class, 'index']);
Route::get('/tag/{maTag}', [TagController::class, 'show']);

// Public routes for TagDiaDiem
Route::get('/tag-dia-diem', [TagDiaDiemController::class, 'index']);
Route::get('/tag-dia-diem/{maTagDiaDiem}', [TagDiaDiemController::class, 'show']);
Route::get('/tag-dia-diem/location/{maDiaDiem}', [TagDiaDiemController::class, 'getTagsByLocation']);
Route::get('/tag-dia-diem/tag/{maTag}', [TagDiaDiemController::class, 'getLocationsByTag']);

// Admin routes
// Route::middleware(['auth:sanctum'])->group(function () {
// Route::post('/admin/login', [AdminController::class, 'login']);
Route::get('/admins', [AdminController::class, 'index']); //

// Admin routes for KhachHang
Route::post('/admin/khach-hang', [KhachHangController::class, 'storeByAdmin']);
Route::delete('/admin/khach-hang/{maKhachHang}', [KhachHangController::class, 'destroy']);

// Admin routes for DiaDiem
Route::post('/dia-diem', [DiaDiemController::class, 'store']);
Route::put('/dia-diem/{maDiaDiem}', [DiaDiemController::class, 'update']);
Route::delete('/dia-diem/{maDiaDiem}', [DiaDiemController::class, 'destroy']);

// Admin routes for Tag
Route::post('/tag', [TagController::class, 'store']);
Route::put('/tag/{maTag}', [TagController::class, 'update']);
Route::delete('/tag/{maTag}', [TagController::class, 'destroy']);

// Admin routes for TagDiaDiem
Route::post('/tag-dia-diem', [TagDiaDiemController::class, 'store']);
Route::put('/tag-dia-diem/{maTagDiaDiem}', [TagDiaDiemController::class, 'update']);
Route::delete('/tag-dia-diem/{maTagDiaDiem}', [TagDiaDiemController::class, 'destroy']);
Route::put('/admins', [AdminController::class, 'updateAll']);
Route::get('/admins/search', [AdminController::class, 'search']);
Route::put('/admin/password', [AdminController::class, 'changePassword']);
Route::post('/admin/store', [AdminController::class, 'store']);
// }
// );
// // Routes cho khách hàng
// Route::middleware('auth:sanctum')->group(function () {
Route::get('/khach-hang/profile', [KhachHangController::class, 'profile']);
Route::get('/khach-hang/profile/{maKhachHang}', [KhachHangController::class, 'profile']);
Route::put('/khach-hang/change-password', [KhachHangController::class, 'changePassword']);
Route::put('/khach-hang/profile/{maKhachHang}', [KhachHangController::class, 'updateProfile']);
Route::put('/khach-hang/change-password/{maKhachHang}', [KhachHangController::class, 'changePassword']);
// });
// Public routes for ChucVu
Route::get('/chuc-vu', [ChucVuController::class, 'index']);

// Public routes for ChucNang
Route::get('/chuc-nang', [ChucNangController::class, 'index']);
Route::post('/chuc-nang', [ChucNangController::class, 'store']);
Route::put('/chuc-nang/{ma_chuc_nang}', [ChucNangController::class, 'update']);
Route::delete('/chuc-nang/{ma_chuc_nang}', [ChucNangController::class, 'destroy']);

// Admin & Other routes for ChucVu
Route::get('/chuc-vu/all', [ChucVuController::class, 'indexAll']);
Route::get('/chuc-vu/search', [ChucVuController::class, 'search']);
Route::get('/chuc-vu/{ma_chuc_vu}', [ChucVuController::class, 'show']);
Route::post('/chuc-vu', [ChucVuController::class, 'store']);
Route::put('/chuc-vu/{ma_chuc_vu}', [ChucVuController::class, 'update']);
Route::delete('/chuc-vu/{ma_chuc_vu}', [ChucVuController::class, 'destroy']);
Route::patch('/chuc-vu/{ma_chuc_vu}/status', [ChucVuController::class, 'changeStatus']);

// Public routes for PhanQuyenAdmin
Route::get('/phan-quyen-admin', [PhanQuyenAdminController::class, 'index']);

// Public routes for Xe
// Route::get('/xe', [XeController::class, 'index']);

// Public routes for XeKeHoach
Route::get('/xe-ke-hoach', [XeKeHoachController::class, 'index']);
// Routes Nhom
Route::get('/nhom/search', [NhomController::class, 'search']);
Route::get('/nhom', [NhomController::class, 'index']);
Route::get('/nhom/{id}', [NhomController::class, 'show']);
Route::post('/nhom', [NhomController::class, 'store']);
Route::put('/nhom/{id}', [NhomController::class, 'update']);
Route::delete('/nhom/{id}', [NhomController::class, 'destroy']);

// Public routes for DichVuDiaDiem
Route::get('/dich-vu-dia-diem', [DichVuDiaDiemController::class, 'index']);
Route::get('/dich-vu-dia-diem/{maDichVu}', [DichVuDiaDiemController::class, 'show']);
Route::get('/dich-vu-dia-diem/search', [DichVuDiaDiemController::class, 'search']);
Route::get('/dich-vu-dia-diem/location/{maDiaDiem}', [DichVuDiaDiemController::class, 'getByLocation']);

// Admin routes for DichVuDiaDiem
Route::get('/dich-vu-dia-diem-all', [DichVuDiaDiemController::class, 'indexAll']);
Route::post('/dich-vu-dia-diem', [DichVuDiaDiemController::class, 'store']);
Route::put('/dich-vu-dia-diem/{maDichVu}', [DichVuDiaDiemController::class, 'update']);
Route::delete('/dich-vu-dia-diem/{maDichVu}', [DichVuDiaDiemController::class, 'destroy']);
Route::patch('/dich-vu-dia-diem/{maDichVu}/status', [DichVuDiaDiemController::class, 'changeStatus']);

// Public routes for KeHoach
Route::get('/ke-hoach', [KeHoachController::class, 'index']);
Route::get('/ke-hoach/{maKeHoach}', [KeHoachController::class, 'show']);
Route::get('/ke-hoach/search', [KeHoachController::class, 'search']);
Route::get('/ke-hoach/group/{maNhom}', [KeHoachController::class, 'getByGroup']);

// Customer routes for KeHoach (Create, Update, Delete)
Route::post('/ke-hoach', [KeHoachController::class, 'store']);
Route::put('/ke-hoach/{maKeHoach}', [KeHoachController::class, 'update']);
Route::delete('/ke-hoach/{maKeHoach}', [KeHoachController::class, 'destroy']);
Route::patch('/ke-hoach/{maKeHoach}/status', [KeHoachController::class, 'changeStatus']);

// Admin route for KeHoach
Route::get('/ke-hoach-all', [KeHoachController::class, 'indexAll']);

// Routes ThanhVienNhom
Route::get('/thanh-vien-nhom/search', [ThanhVienNhomController::class, 'search']);
Route::get('/thanh-vien-nhom/nhom/{maNhom}', [ThanhVienNhomController::class, 'getByNhom']);
Route::get('/thanh-vien-nhom', [ThanhVienNhomController::class, 'index']);
Route::get('/thanh-vien-nhom/{id}', [ThanhVienNhomController::class, 'show']);
Route::post('/thanh-vien-nhom', [ThanhVienNhomController::class, 'store']);
Route::put('/thanh-vien-nhom/{id}', [ThanhVienNhomController::class, 'update']);
Route::delete('/thanh-vien-nhom/{id}', [ThanhVienNhomController::class, 'destroy']);

// Routes DanhGiaKeHoach
Route::get('/danh-gia-ke-hoach/search', [DanhGiaKeHoachController::class, 'search']);
Route::get('/danh-gia-ke-hoach/dia-diem/{maDiaDiem}', [DanhGiaKeHoachController::class, 'getByDiaDiem']);
Route::get('/danh-gia-ke-hoach', [DanhGiaKeHoachController::class, 'index']);
Route::get('/danh-gia-ke-hoach/{id}', [DanhGiaKeHoachController::class, 'show']);
Route::post('/danh-gia-ke-hoach', [DanhGiaKeHoachController::class, 'store']);
Route::put('/danh-gia-ke-hoach/{id}', [DanhGiaKeHoachController::class, 'update']);
Route::delete('/danh-gia-ke-hoach/{id}', [DanhGiaKeHoachController::class, 'destroy']);

// Routes HoaDon
// Khach hang
Route::get('/khach-hang/hoa-don', [HoaDonController::class, 'indexCustomer']);
Route::get('/khach-hang/hoa-don/{ma_hoa_don}', [HoaDonController::class, 'showCustomer']);
Route::post('/khach-hang/hoa-don', [HoaDonController::class, 'storeCustomer']);

// Routes DanhSachYeuThich cho khach hang
Route::get('/khach-hang/danh-sach-yeu-thich', [DanhSachYeuThichController::class, 'indexCustomer']);
Route::post('/khach-hang/danh-sach-yeu-thich', [DanhSachYeuThichController::class, 'storeCustomer']);
Route::delete('/khach-hang/danh-sach-yeu-thich/{ma_danh_sach_ua_thich}', [DanhSachYeuThichController::class, 'destroyCustomer']);

// Admin
Route::get('/admin/hoa-don', [HoaDonController::class, 'indexAdmin']);
Route::get('/admin/hoa-don/{ma_hoa_don}', [HoaDonController::class, 'showAdmin']);
Route::patch('/admin/hoa-don/{ma_hoa_don}/status', [HoaDonController::class, 'updateStatusAdmin']);
// Admin routes for Xe
Route::get('/xe/all', [XeController::class, 'indexAll']);
Route::get('/xe/search', [XeController::class, 'search']);
Route::get('/xe/{ma_xe}', [XeController::class, 'show']);
Route::post('/xe', [XeController::class, 'store']);
Route::put('/xe/{ma_xe}', [XeController::class, 'update']);
Route::patch('/xe/{ma_xe}/status', [XeController::class, 'changeStatus']);
Route::delete('/xe/{ma_xe}', [XeController::class, 'destroy']);

// Admin routes for XeKeHoach
Route::get('/xe-ke-hoach/all', [XeKeHoachController::class, 'indexAll']);
Route::get('/xe-ke-hoach/search', [XeKeHoachController::class, 'search']);
Route::get('/xe-ke-hoach/{ma_xe_ke_hoach}', [XeKeHoachController::class, 'show']);
Route::post('/xe-ke-hoach', [XeKeHoachController::class, 'store']);
Route::put('/xe-ke-hoach/{ma_xe_ke_hoach}', [XeKeHoachController::class, 'update']);
Route::delete('/xe-ke-hoach/{ma_xe_ke_hoach}', [XeKeHoachController::class, 'destroy']);
// Public routes for Tour
Route::get('/tour', [TourController::class, 'index']);
Route::get('/tour/{ma_tour}', [TourController::class, 'show']);

// Admin routes for Tour
Route::post('/tour', [TourController::class, 'store']);
Route::put('/tour/{ma_tour}', [TourController::class, 'update']);
Route::delete('/tour/{ma_tour}', [TourController::class, 'destroy']);

// Public routes for ChiTietTour
Route::get('/chi-tiet-tour', [ChiTietTourController::class, 'index']);
Route::get('/chi-tiet-tour/{ma_chi_tiet_tour}', [ChiTietTourController::class, 'show']);

// Admin routes for ChiTietTour
Route::post('/chi-tiet-tour', [ChiTietTourController::class, 'store']);
Route::put('/chi-tiet-tour/{ma_chi_tiet_tour}', [ChiTietTourController::class, 'update']);
Route::delete('/chi-tiet-tour/{ma_chi_tiet_tour}', [ChiTietTourController::class, 'destroy']);

// Public routes for TourKhoiHanh
Route::get('/tour-khoi-hanh', [TourKhoiHanhController::class, 'index']);
Route::get('/tour-khoi-hanh/{ma_thoi_gian_tour}', [TourKhoiHanhController::class, 'show']);

// Admin routes for TourKhoiHanh
Route::post('/tour-khoi-hanh', [TourKhoiHanhController::class, 'store']);
Route::put('/tour-khoi-hanh/{ma_thoi_gian_tour}', [TourKhoiHanhController::class, 'update']);
Route::delete('/tour-khoi-hanh/{ma_thoi_gian_tour}', [TourKhoiHanhController::class, 'destroy']);

// Admin routes for CauHinhNgay
Route::get('/admin/cau-hinh-ngay', [CauHinhNgayController::class, 'index']);
Route::get('/admin/cau-hinh-ngay/{ma_cau_hinh_ngay}', [CauHinhNgayController::class, 'show']);
Route::post('/admin/cau-hinh-ngay', [CauHinhNgayController::class, 'store']);
Route::put('/admin/cau-hinh-ngay/{ma_cau_hinh_ngay}', [CauHinhNgayController::class, 'update']);
Route::delete('/admin/cau-hinh-ngay/{ma_cau_hinh_ngay}', [CauHinhNgayController::class, 'destroy']);

// Routes Admin Controller with {id} (placed at end to avoid intercepting specific routes)
Route::get('/admin/{id}', [AdminController::class, 'show']);
Route::put('/admin/{id}', [AdminController::class, 'update']);
Route::delete('/admin/{id}', [AdminController::class, 'delete']);
Route::patch('/admin/{id}/status', [AdminController::class, 'changeStatus']);

// Routes HoatDongChiTiet cho khach hang dang nhap
use App\Http\Controllers\HoatDongChiTietController;
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/hoat-dong-chi-tiet', [HoatDongChiTietController::class, 'index']);
    Route::get('/hoat-dong-chi-tiet/{id}', [HoatDongChiTietController::class, 'show']);
    Route::post('/hoat-dong-chi-tiet', [HoatDongChiTietController::class, 'store']);
    Route::put('/hoat-dong-chi-tiet/{id}', [HoatDongChiTietController::class, 'update']);
    Route::delete('/hoat-dong-chi-tiet/{id}', [HoatDongChiTietController::class, 'destroy']);
});
