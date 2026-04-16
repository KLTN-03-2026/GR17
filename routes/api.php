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
use App\Http\Controllers\AIPlannerController;
use App\Http\Controllers\AIConfigController;
use App\Http\Controllers\DoiTacAuthController;
use App\Http\Controllers\DoiTacAdminController;
use App\Http\Controllers\DoiTacDiaDiemController;
use App\Http\Controllers\DoiTacDiaDiemModerationController;
use App\Http\Controllers\DoiTacTourController;
use App\Http\Controllers\DoiTacTourModerationController;
use App\Http\Controllers\DoiTacTourDiaDiemController;
use App\Http\Controllers\DoiTacDichVuDiaDiemController;
use App\Http\Controllers\AdminDoiSoatController;
use App\Http\Controllers\DoiTacDoiSoatController;
use App\Http\Controllers\DoiTacOrderController;
use App\Http\Controllers\CustomerTourPaymentController;
use App\Http\Controllers\SepayWebhookController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Public routes
Route::prefix('khach-hang')->group(function () {
    Route::post('/ke-hoach-ai/de-xuat-dia-diem', [AIPlannerController::class, 'suggestLocations']);
    Route::post('/ke-hoach-ai', [AIPlannerController::class, 'generateItinerary']);
    Route::post('/ke-hoach-ai/save', [AIPlannerController::class, 'saveItinerary']);
});

// Debug routes for AI
Route::get('/debug/gemini', [AIPlannerController::class, 'testGemini']);
Route::get('/debug/openai', [AIPlannerController::class, 'testOpenAI']);

Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/khach-hang/register', [KhachHangController::class, 'register']);
Route::post('/khach-hang/login', [KhachHangController::class, 'login']);
Route::post('/doi-tac/register', [DoiTacAuthController::class, 'register']);
Route::post('/doi-tac/login', [DoiTacAuthController::class, 'login']);
Route::post('/payments/sepay/webhook', [SepayWebhookController::class, 'handle']);
Route::middleware('auth:sanctum')->get('/admin/check-login', [AdminController::class, 'checkLogin']);
Route::middleware('auth:sanctum')->get('/khach-hang/check-login', [KhachHangController::class, 'checkLogin']);
Route::middleware('auth:sanctum')->get('/doi-tac/check-login', [DoiTacAuthController::class, 'checkLogin']);

Route::middleware(['auth:sanctum', 'admin.auth'])->group(function () {
    Route::get('/admin/doi-tac', [DoiTacAdminController::class, 'index']);
    Route::post('/admin/doi-tac', [DoiTacAdminController::class, 'store']);
    Route::get('/admin/doi-tac/pending', [DoiTacAdminController::class, 'pending']);
    Route::patch('/admin/doi-tac/{ma_doi_tac}/approve', [DoiTacAdminController::class, 'approve']);
    Route::patch('/admin/doi-tac/{ma_doi_tac}/reject', [DoiTacAdminController::class, 'reject']);

    Route::get('/admin/doi-tac/dia-diem', [DoiTacDiaDiemModerationController::class, 'index']);
    Route::get('/admin/doi-tac/dia-diem/pending', [DoiTacDiaDiemModerationController::class, 'pending']);
    Route::patch('/admin/doi-tac/dia-diem/{ma_dia_diem}/approve', [DoiTacDiaDiemModerationController::class, 'approve']);
    Route::patch('/admin/doi-tac/dia-diem/{ma_dia_diem}/reject', [DoiTacDiaDiemModerationController::class, 'reject']);

    Route::get('/admin/doi-tac/tour/pending', [DoiTacTourModerationController::class, 'pending']);
    Route::patch('/admin/doi-tac/tour/{ma_tour}/approve', [DoiTacTourModerationController::class, 'approve']);
    Route::patch('/admin/doi-tac/tour/{ma_tour}/reject', [DoiTacTourModerationController::class, 'reject']);
    Route::patch('/admin/doi-tac/tour/{ma_tour}/visibility', [DoiTacTourModerationController::class, 'visibility']);

    Route::get('/admin/doi-tac/{ma_doi_tac}', [DoiTacAdminController::class, 'show']);
    Route::put('/admin/doi-tac/{ma_doi_tac}', [DoiTacAdminController::class, 'update']);
    Route::patch('/admin/doi-tac/{ma_doi_tac}/status', [DoiTacAdminController::class, 'changeStatus']);
    Route::post('/admin/khach-hang', [KhachHangController::class, 'storeByAdmin']);
    Route::delete('/admin/khach-hang/{maKhachHang}', [KhachHangController::class, 'destroy']);
    Route::get('/admin/hoa-don', [HoaDonController::class, 'indexAdmin']);
    Route::get('/admin/hoa-don/{ma_hoa_don}', [HoaDonController::class, 'showAdmin']);
    Route::patch('/admin/hoa-don/{ma_hoa_don}/status', [HoaDonController::class, 'updateStatusAdmin']);

    // API Admin thống kê & thanh toán (Đối soát tài chính)
    Route::get('/admin/doi-soat', [AdminDoiSoatController::class, 'index']);
    Route::patch('/admin/doi-soat/{ma_doi_soat}/pay', [AdminDoiSoatController::class, 'markAsPaid']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/khach-hang', [KhachHangController::class, 'index']);
    Route::get('/khach-hang/profile', [KhachHangController::class, 'profile']);
    Route::get('/khach-hang/profile/{maKhachHang}', [KhachHangController::class, 'profile']);
    Route::put('/khach-hang/profile/{maKhachHang}', [KhachHangController::class, 'updateProfile']);
    Route::put('/khach-hang/change-password/{maKhachHang}', [KhachHangController::class, 'changePassword']);
    Route::get('/khach-hang/hoa-don', [HoaDonController::class, 'indexCustomer']);
    Route::get('/khach-hang/hoa-don/{ma_hoa_don}', [HoaDonController::class, 'showCustomer']);
    Route::get('/khach-hang/hoa-don/{ma_hoa_don}/payment-status', [HoaDonController::class, 'paymentStatus']);
    Route::post('/khach-hang/hoa-don/{ma_hoa_don}/retry-payment', [HoaDonController::class, 'retryPayment']);
    Route::post('/khach-hang/hoa-don/{ma_hoa_don}/cancel-payment', [HoaDonController::class, 'cancelPayment']);
    Route::post('/khach-hang/hoa-don', [HoaDonController::class, 'storeCustomer']);
    Route::post('/khach-hang/tour/{ma_tour}/thanh-toan/qr', [CustomerTourPaymentController::class, 'store']);

    Route::get('/doi-tac/dia-diem', [DoiTacDiaDiemController::class, 'index']);
    Route::get('/doi-tac/dia-diem/{ma_dia_diem}', [DoiTacDiaDiemController::class, 'show']);
    Route::post('/doi-tac/dia-diem', [DoiTacDiaDiemController::class, 'store']);
    Route::put('/doi-tac/dia-diem/{ma_dia_diem}', [DoiTacDiaDiemController::class, 'update']);
    Route::delete('/doi-tac/dia-diem/{ma_dia_diem}', [DoiTacDiaDiemController::class, 'destroy']);

    Route::get('/doi-tac/dia-diem/{ma_dia_diem}/dich-vu', [DoiTacDichVuDiaDiemController::class, 'index']);
    Route::post('/doi-tac/dia-diem/{ma_dia_diem}/dich-vu', [DoiTacDichVuDiaDiemController::class, 'store']);
    Route::put('/doi-tac/dia-diem/{ma_dia_diem}/dich-vu/{ma_dich_vu}', [DoiTacDichVuDiaDiemController::class, 'update']);
    Route::delete('/doi-tac/dia-diem/{ma_dia_diem}/dich-vu/{ma_dich_vu}', [DoiTacDichVuDiaDiemController::class, 'destroy']);

    Route::get('/doi-tac/tour', [DoiTacTourController::class, 'index']);
    Route::get('/doi-tac/tour/{ma_tour}', [DoiTacTourController::class, 'show']);
    Route::post('/doi-tac/tour', [DoiTacTourController::class, 'store']);
    Route::put('/doi-tac/tour/{ma_tour}', [DoiTacTourController::class, 'update']);
    Route::delete('/doi-tac/tour/{ma_tour}', [DoiTacTourController::class, 'destroy']);
    Route::post('/doi-tac/tour/{ma_tour}/submit', [DoiTacTourController::class, 'submit']);

    Route::get('/doi-tac/tour/{ma_tour}/dia-diem', [DoiTacTourDiaDiemController::class, 'index']);
    Route::post('/doi-tac/tour/{ma_tour}/dia-diem/existing', [DoiTacTourDiaDiemController::class, 'attachExisting']);
    Route::post('/doi-tac/tour/{ma_tour}/dia-diem/create-and-attach', [DoiTacTourDiaDiemController::class, 'createAndAttach']);
    Route::put('/doi-tac/tour/{ma_tour}/dia-diem/{ma_chi_tiet_tour}', [DoiTacTourDiaDiemController::class, 'update']);
    Route::delete('/doi-tac/tour/{ma_tour}/dia-diem/{ma_chi_tiet_tour}', [DoiTacTourDiaDiemController::class, 'destroy']);

    // API Doanh thu Đối tác
    Route::get('/doi-tac/doanh-thu', [DoiTacDoiSoatController::class, 'index']);

    // API Quản lý Đơn hàng (Khách mua tour của đối tác)
    Route::get('/doi-tac/don-hang', [DoiTacOrderController::class, 'index']);
});

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
Route::middleware('auth:sanctum')->put('/admin/password', [AdminController::class, 'changePassword']);
Route::post('/admin/store', [AdminController::class, 'store']);
// }
// );
// // Routes cho khách hàng
// Route::middleware('auth:sanctum')->group(function () {
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
Route::get('/phan-quyen-admin/{ma_phan_quyen}', [PhanQuyenAdminController::class, 'show']);
Route::post('/phan-quyen-admin', [PhanQuyenAdminController::class, 'store']);
Route::put('/phan-quyen-admin/{ma_phan_quyen}', [PhanQuyenAdminController::class, 'update']);
Route::delete('/phan-quyen-admin/{ma_phan_quyen}', [PhanQuyenAdminController::class, 'destroy']);

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
Route::get('/ke-hoach/{maKeHoach}/goi-y-lan-can', [KeHoachController::class, 'suggestNearby']);
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

// Routes DanhSachYeuThich cho khach hang
Route::get('/khach-hang/danh-sach-yeu-thich', [DanhSachYeuThichController::class, 'indexCustomer']);
Route::post('/khach-hang/danh-sach-yeu-thich', [DanhSachYeuThichController::class, 'storeCustomer']);
Route::put('/khach-hang/danh-sach-yeu-thich/{ma_danh_sach_ua_thich}', [DanhSachYeuThichController::class, 'updateCustomer']);
Route::delete('/khach-hang/danh-sach-yeu-thich/{ma_danh_sach_ua_thich}', [DanhSachYeuThichController::class, 'destroyCustomer']);

// Admin AI Configuration
Route::get('/admin/cau-hinh-ai', [AIConfigController::class, 'getPrompt']);
Route::post('/admin/cau-hinh-ai', [AIConfigController::class, 'updatePrompt']);
Route::get('/admin/cau-hinh-ai/api-config', [AIConfigController::class, 'getApiConfig']);
Route::post('/admin/cau-hinh-ai/api-config', [AIConfigController::class, 'updateApiConfig']);
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
