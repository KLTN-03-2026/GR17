<?php

use App\Http\Controllers\AIConfigController;
use App\Http\Controllers\AIPlannerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDoiSoatController;
use App\Http\Controllers\AdminStatisticController;
use App\Http\Controllers\AdminVoucherController;
use App\Http\Controllers\CauHinhNgayController;
use App\Http\Controllers\ChiTietTourController;
use App\Http\Controllers\ChucNangController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\CustomerTourPaymentController;
use App\Http\Controllers\DanhGiaKeHoachController;
use App\Http\Controllers\DanhSachYeuThichController;
use App\Http\Controllers\DiaDiemController;
use App\Http\Controllers\DichVuDiaDiemController;
use App\Http\Controllers\DoiTacAdminController;
use App\Http\Controllers\DoiTacAuthController;
use App\Http\Controllers\DoiTacDiaDiemController;
use App\Http\Controllers\DoiTacDiaDiemModerationController;
use App\Http\Controllers\DoiTacDichVuDiaDiemController;
use App\Http\Controllers\DoiTacDoiSoatController;
use App\Http\Controllers\DoiTacOrderController;
use App\Http\Controllers\DoiTacTourController;
use App\Http\Controllers\DoiTacTourDiaDiemController;
use App\Http\Controllers\DoiTacTourModerationController;
use App\Http\Controllers\DoiTacVoucherController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\HoatDongChiTietController;
use App\Http\Controllers\KeHoachController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\NhomController;
use App\Http\Controllers\PartnerStatisticController;
use App\Http\Controllers\PhanHoiController;
use App\Http\Controllers\PhanQuyenAdminController;
use App\Http\Controllers\SepayWebhookController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TagDiaDiemController;
use App\Http\Controllers\ThanhVienNhomController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\TourKhoiHanhController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\XeController;
use App\Http\Controllers\XeKeHoachController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/khach-hang/ke-hoach-ai', [AIPlannerController::class, 'generateItinerary']);
Route::post('/khach-hang/ke-hoach-ai/de-xuat-dia-diem', [AIPlannerController::class, 'suggestLocations']);
Route::post('/khach-hang/ke-hoach-ai/goi-y-tour-phu-hop', [AIPlannerController::class, 'suggestRefinedTours']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/khach-hang/register', [KhachHangController::class, 'register']);
Route::post('/khach-hang/login', [KhachHangController::class, 'login']);
Route::post('/khach-hang/phan-hoi', [PhanHoiController::class, 'store']);
Route::get('/khach-hang/voucher/available', [VoucherController::class, 'getAvailableVouchers']);
Route::post('/khach-hang/voucher/apply', [VoucherController::class, 'applyVoucher']);
Route::post('/doi-tac/register', [DoiTacAuthController::class, 'register']);
Route::post('/doi-tac/login', [DoiTacAuthController::class, 'login']);
Route::post('/payments/sepay/webhook', [SepayWebhookController::class, 'handle']);

Route::get('/dia-diem', [DiaDiemController::class, 'index']);
Route::get('/dia-diem/{maDiaDiem}', [DiaDiemController::class, 'show']);
Route::get('/dia-diem/filter/type/{loai}', [DiaDiemController::class, 'filterByType']);
Route::get('/dia-diem/filter/tag/{maTag}', [DiaDiemController::class, 'filterByTag']);
Route::get('/dia-diem/{maDiaDiem}/tuong-tu', [DiaDiemController::class, 'similar']);

Route::get('/tag', [TagController::class, 'index']);
Route::get('/tag/{maTag}', [TagController::class, 'show']);

Route::get('/tag-dia-diem', [TagDiaDiemController::class, 'index']);
Route::get('/tag-dia-diem/{maTagDiaDiem}', [TagDiaDiemController::class, 'show']);
Route::get('/tag-dia-diem/location/{maDiaDiem}', [TagDiaDiemController::class, 'getTagsByLocation']);
Route::get('/tag-dia-diem/tag/{maTag}', [TagDiaDiemController::class, 'getLocationsByTag']);

Route::get('/chuc-vu', [ChucVuController::class, 'index']);
Route::get('/chuc-vu/all', [ChucVuController::class, 'indexAll']);
Route::get('/chuc-vu/search', [ChucVuController::class, 'search']);
Route::get('/chuc-vu/{ma_chuc_vu}', [ChucVuController::class, 'show']);

Route::get('/chuc-nang', [ChucNangController::class, 'index']);

Route::get('/dich-vu-dia-diem', [DichVuDiaDiemController::class, 'index']);
Route::get('/dich-vu-dia-diem/{maDichVu}', [DichVuDiaDiemController::class, 'show']);
Route::get('/dich-vu-dia-diem/search', [DichVuDiaDiemController::class, 'search']);
Route::get('/dich-vu-dia-diem/location/{maDiaDiem}', [DichVuDiaDiemController::class, 'getByLocation']);

Route::get('/tour', [TourController::class, 'index']);
Route::get('/tour/{ma_tour}', [TourController::class, 'show']);

Route::get('/chi-tiet-tour', [ChiTietTourController::class, 'index']);
Route::get('/chi-tiet-tour/{ma_chi_tiet_tour}', [ChiTietTourController::class, 'show']);

Route::get('/tour-khoi-hanh', [TourKhoiHanhController::class, 'index']);
Route::get('/tour-khoi-hanh/{ma_thoi_gian_tour}', [TourKhoiHanhController::class, 'show']);

Route::get('/xe-ke-hoach', [XeKeHoachController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/check-login', [AdminController::class, 'checkLogin']);
    Route::get('/khach-hang/check-login', [KhachHangController::class, 'checkLogin']);
    Route::get('/doi-tac/check-login', [DoiTacAuthController::class, 'checkLogin']);
    Route::post('/admin/logout', [AdminController::class, 'logout']);
    Route::post('/khach-hang/logout', [KhachHangController::class, 'logout']);
    Route::post('/doi-tac/logout', [DoiTacAuthController::class, 'logout']);
    Route::put('/doi-tac/profile', [DoiTacAuthController::class, 'updateProfile']);
    Route::put('/doi-tac/change-password', [DoiTacAuthController::class, 'changePassword']);

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
    Route::post('/khach-hang/ke-hoach-ai/save', [AIPlannerController::class, 'saveItinerary']);

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
    Route::get('/doi-tac/doanh-thu', [DoiTacDoiSoatController::class, 'index']);
    Route::get('/doi-tac/don-hang', [DoiTacOrderController::class, 'index']);
    Route::get('/doi-tac/statistics/revenue', [PartnerStatisticController::class, 'getPartnerRevenue']);
    Route::get('/doi-tac/statistics/ratings', [PartnerStatisticController::class, 'getFeedbackAndRatings']);
    Route::get('/doi-tac/statistics/tours', [PartnerStatisticController::class, 'getTourPerformance']);
    Route::patch('/doi-tac/vouchers/{voucher}/status', [DoiTacVoucherController::class, 'changeStatus']);
    Route::apiResource('/doi-tac/vouchers', DoiTacVoucherController::class);

    Route::get('/nhom/search', [NhomController::class, 'search']);
    Route::get('/nhom', [NhomController::class, 'index']);
    Route::get('/nhom/{id}', [NhomController::class, 'show']);
    Route::post('/nhom', [NhomController::class, 'store']);
    Route::put('/nhom/{id}', [NhomController::class, 'update']);
    Route::delete('/nhom/{id}', [NhomController::class, 'destroy']);

    Route::get('/thanh-vien-nhom/search', [ThanhVienNhomController::class, 'search']);
    Route::get('/thanh-vien-nhom/nhom/{maNhom}', [ThanhVienNhomController::class, 'getByNhom']);
    Route::get('/thanh-vien-nhom', [ThanhVienNhomController::class, 'index']);
    Route::get('/thanh-vien-nhom/{id}', [ThanhVienNhomController::class, 'show']);
    Route::post('/thanh-vien-nhom', [ThanhVienNhomController::class, 'store']);
    Route::put('/thanh-vien-nhom/{id}', [ThanhVienNhomController::class, 'update']);
    Route::delete('/thanh-vien-nhom/{id}', [ThanhVienNhomController::class, 'destroy']);

    Route::get('/ke-hoach', [KeHoachController::class, 'index']);
    Route::post('/ke-hoach/{maKeHoach}/lich-trinh-ai', [KeHoachController::class, 'generateAiTimeline']);
    Route::get('/ke-hoach/{maKeHoach}/hoat-dong', [HoatDongChiTietController::class, 'indexByPlan']);
    Route::post('/ke-hoach/{maKeHoach}/hoat-dong', [HoatDongChiTietController::class, 'storeForPlan']);
    Route::put('/ke-hoach/{maKeHoach}/hoat-dong/{activityId}', [HoatDongChiTietController::class, 'updateForPlan']);
    Route::delete('/ke-hoach/{maKeHoach}/hoat-dong/{activityId}', [HoatDongChiTietController::class, 'destroyForPlan']);
    Route::get('/ke-hoach/{maKeHoach}', [KeHoachController::class, 'show']);
    Route::get('/ke-hoach/{maKeHoach}/goi-y-lan-can', [KeHoachController::class, 'suggestNearby']);
    Route::get('/ke-hoach/search', [KeHoachController::class, 'search']);
    Route::get('/ke-hoach/group/{maNhom}', [KeHoachController::class, 'getByGroup']);
    Route::post('/ke-hoach', [KeHoachController::class, 'store']);
    Route::put('/ke-hoach/{maKeHoach}', [KeHoachController::class, 'update']);
    Route::delete('/ke-hoach/{maKeHoach}', [KeHoachController::class, 'destroy']);
    Route::patch('/ke-hoach/{maKeHoach}/status', [KeHoachController::class, 'changeStatus']);

    Route::get('/danh-gia-ke-hoach/search', [DanhGiaKeHoachController::class, 'search']);
    Route::get('/danh-gia-ke-hoach/dia-diem/{maDiaDiem}', [DanhGiaKeHoachController::class, 'getByDiaDiem']);
    Route::get('/danh-gia-ke-hoach', [DanhGiaKeHoachController::class, 'index']);
    Route::get('/danh-gia-ke-hoach/{id}', [DanhGiaKeHoachController::class, 'show']);
    Route::post('/danh-gia-ke-hoach', [DanhGiaKeHoachController::class, 'store']);
    Route::put('/danh-gia-ke-hoach/{id}', [DanhGiaKeHoachController::class, 'update']);
    Route::delete('/danh-gia-ke-hoach/{id}', [DanhGiaKeHoachController::class, 'destroy']);

    Route::get('/khach-hang/danh-sach-yeu-thich', [DanhSachYeuThichController::class, 'indexCustomer']);
    Route::post('/khach-hang/danh-sach-yeu-thich', [DanhSachYeuThichController::class, 'storeCustomer']);
    Route::put('/khach-hang/danh-sach-yeu-thich/{ma_danh_sach_ua_thich}', [DanhSachYeuThichController::class, 'updateCustomer']);
    Route::delete('/khach-hang/danh-sach-yeu-thich/{ma_danh_sach_ua_thich}', [DanhSachYeuThichController::class, 'destroyCustomer']);

    Route::get('/hoat-dong-chi-tiet', [HoatDongChiTietController::class, 'index']);
    Route::get('/hoat-dong-chi-tiet/{id}', [HoatDongChiTietController::class, 'show']);
    Route::post('/hoat-dong-chi-tiet', [HoatDongChiTietController::class, 'store']);
    Route::put('/hoat-dong-chi-tiet/{id}', [HoatDongChiTietController::class, 'update']);
    Route::delete('/hoat-dong-chi-tiet/{id}', [HoatDongChiTietController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'admin.auth'])->group(function () {
    // 1. Quản lý Đối tác (101)
    Route::middleware('check.permission:101')->group(function () {
        Route::get('/admin/doi-tac', [DoiTacAdminController::class, 'index']);
        Route::get('/admin/doi-tac/pending', [DoiTacAdminController::class, 'pending']);
        Route::post('/admin/doi-tac', [DoiTacAdminController::class, 'store']);
        Route::get('/admin/doi-tac/{ma_doi_tac}', [DoiTacAdminController::class, 'show']);
        Route::put('/admin/doi-tac/{ma_doi_tac}', [DoiTacAdminController::class, 'update']);
        Route::patch('/admin/doi-tac/{ma_doi_tac}/approve', [DoiTacAdminController::class, 'approve']);
        Route::patch('/admin/doi-tac/{ma_doi_tac}/reject', [DoiTacAdminController::class, 'reject']);
        Route::patch('/admin/doi-tac/{ma_doi_tac}/status', [DoiTacAdminController::class, 'changeStatus']);
        
        Route::get('/admin/doi-tac/dia-diem', [DoiTacDiaDiemModerationController::class, 'index']);
        Route::get('/admin/doi-tac/dia-diem/pending', [DoiTacDiaDiemModerationController::class, 'pending']);
        Route::patch('/admin/doi-tac/dia-diem/{ma_dia_diem}/approve', [DoiTacDiaDiemModerationController::class, 'approve']);
        Route::patch('/admin/doi-tac/dia-diem/{ma_dia_diem}/reject', [DoiTacDiaDiemModerationController::class, 'reject']);

        Route::get('/admin/doi-tac/tour/pending', [DoiTacTourModerationController::class, 'pending']);
        Route::patch('/admin/doi-tac/tour/{ma_tour}/approve', [DoiTacTourModerationController::class, 'approve']);
        Route::patch('/admin/doi-tac/tour/{ma_tour}/reject', [DoiTacTourModerationController::class, 'reject']);
        Route::patch('/admin/doi-tac/tour/{ma_tour}/visibility', [DoiTacTourModerationController::class, 'visibility']);
    });

    // 2. Quản lý Khách hàng & Hóa đơn (102)
    Route::middleware('check.permission:102')->group(function () {
        Route::post('/admin/khach-hang', [KhachHangController::class, 'storeByAdmin']);
        Route::delete('/admin/khach-hang/{maKhachHang}', [KhachHangController::class, 'destroy']);
        Route::get('/admin/hoa-don', [HoaDonController::class, 'indexAdmin']);
        Route::get('/admin/hoa-don/{ma_hoa_don}', [HoaDonController::class, 'showAdmin']);
        Route::patch('/admin/hoa-don/{ma_hoa_don}/status', [HoaDonController::class, 'updateStatusAdmin']);
        Route::get('/admin/phan-hoi', [PhanHoiController::class, 'index']);
        Route::put('/admin/phan-hoi/{id}/trang-thai', [PhanHoiController::class, 'updateStatus']);
    });

    // 3. Quản lý Địa điểm (103)
    Route::middleware('check.permission:103')->group(function () {
        Route::post('/dia-diem', [DiaDiemController::class, 'store']);
        Route::put('/dia-diem/{maDiaDiem}', [DiaDiemController::class, 'update']);
        Route::delete('/dia-diem/{maDiaDiem}', [DiaDiemController::class, 'destroy']);
        
        Route::post('/tag', [TagController::class, 'store']);
        Route::put('/tag/{maTag}', [TagController::class, 'update']);
        Route::delete('/tag/{maTag}', [TagController::class, 'destroy']);
        
        Route::post('/tag-dia-diem', [TagDiaDiemController::class, 'store']);
        Route::put('/tag-dia-diem/{maTagDiaDiem}', [TagDiaDiemController::class, 'update']);
        Route::delete('/tag-dia-diem/{maTagDiaDiem}', [TagDiaDiemController::class, 'destroy']);

        Route::get('/dich-vu-dia-diem-all', [DichVuDiaDiemController::class, 'indexAll']);
        Route::post('/dich-vu-dia-diem', [DichVuDiaDiemController::class, 'store']);
        Route::put('/dich-vu-dia-diem/{maDichVu}', [DichVuDiaDiemController::class, 'update']);
        Route::delete('/dich-vu-dia-diem/{maDichVu}', [DichVuDiaDiemController::class, 'destroy']);
        Route::patch('/dich-vu-dia-diem/{maDichVu}/status', [DichVuDiaDiemController::class, 'changeStatus']);
    });

    // 4. Quản lý Tour & Phương tiện (104)
    Route::middleware('check.permission:104')->group(function () {
        Route::post('/tour', [TourController::class, 'store']);
        Route::put('/tour/{ma_tour}', [TourController::class, 'update']);
        Route::delete('/tour/{ma_tour}', [TourController::class, 'destroy']);

        Route::post('/chi-tiet-tour', [ChiTietTourController::class, 'store']);
        Route::put('/chi-tiet-tour/{ma_chi_tiet_tour}', [ChiTietTourController::class, 'update']);
        Route::delete('/chi-tiet-tour/{ma_chi_tiet_tour}', [ChiTietTourController::class, 'destroy']);

        Route::post('/tour-khoi-hanh', [TourKhoiHanhController::class, 'store']);
        Route::put('/tour-khoi-hanh/{ma_thoi_gian_tour}', [TourKhoiHanhController::class, 'update']);
        Route::delete('/tour-khoi-hanh/{ma_thoi_gian_tour}', [TourKhoiHanhController::class, 'destroy']);

        Route::get('/xe/all', [XeController::class, 'indexAll']);
        Route::get('/xe/search', [XeController::class, 'search']);
        Route::post('/xe', [XeController::class, 'store']);
        Route::put('/xe/{ma_xe}', [XeController::class, 'update']);
        Route::delete('/xe/{ma_xe}', [XeController::class, 'destroy']);
        Route::patch('/xe/{ma_xe}/status', [XeController::class, 'changeStatus']);
        
        Route::get('/xe-ke-hoach/all', [XeKeHoachController::class, 'indexAll']);
        Route::post('/xe-ke-hoach', [XeKeHoachController::class, 'store']);
        Route::put('/xe-ke-hoach/{ma_xe_ke_hoach}', [XeKeHoachController::class, 'update']);
        Route::delete('/xe-ke-hoach/{ma_xe_ke_hoach}', [XeKeHoachController::class, 'destroy']);
    });

    // 5. Quản lý Voucher & Tài chính (105)
    Route::middleware('check.permission:105')->group(function () {
        Route::apiResource('/admin/vouchers', AdminVoucherController::class);
        Route::patch('/admin/vouchers/{voucher}/status', [AdminVoucherController::class, 'changeStatus']);
        Route::get('/admin/doi-soat', [AdminDoiSoatController::class, 'index']);
        Route::patch('/admin/doi-soat/{ma_doi_soat}/pay', [AdminDoiSoatController::class, 'markAsPaid']);
    });

    // 6. Thống kê & Báo cáo (106)
    Route::middleware('check.permission:106')->group(function () {
        Route::get('/admin/statistics/revenue', [AdminStatisticController::class, 'getPlatformRevenue']);
        Route::get('/admin/statistics/users', [AdminStatisticController::class, 'getUserStatistics']);
        Route::get('/admin/statistics/locations-tours', [AdminStatisticController::class, 'getLocationAndTourStatistics']);
        Route::get('/admin/statistics/reconciliation', [AdminStatisticController::class, 'getReconciliationStatistics']);
        Route::get('/ke-hoach-all', [KeHoachController::class, 'indexAll']);
    });

    // 7. Quản lý Hệ thống (107)
    Route::middleware('check.permission:107')->group(function () {
        Route::get('/admins', [AdminController::class, 'index']);
        Route::post('/admin/store', [AdminController::class, 'store']);
        Route::get('/admin/{id}', [AdminController::class, 'show']);
        Route::put('/admin/{id}', [AdminController::class, 'update']);
        Route::delete('/admin/{id}', [AdminController::class, 'delete']);
        Route::patch('/admin/{id}/status', [AdminController::class, 'changeStatus']);
        Route::get('/admins/search', [AdminController::class, 'search']);
        Route::put('/admins', [AdminController::class, 'updateAll']);
        Route::put('/admin/password', [AdminController::class, 'changePassword']);

        Route::post('/chuc-nang', [ChucNangController::class, 'store']);
        Route::put('/chuc-nang/{ma_chuc_nang}', [ChucNangController::class, 'update']);
        Route::delete('/chuc-nang/{ma_chuc_nang}', [ChucNangController::class, 'destroy']);
        
        Route::post('/chuc-vu', [ChucVuController::class, 'store']);
        Route::put('/chuc-vu/{ma_chuc_vu}', [ChucVuController::class, 'update']);
        Route::delete('/chuc-vu/{ma_chuc_vu}', [ChucVuController::class, 'destroy']);
        Route::patch('/chuc-vu/{ma_chuc_vu}/status', [ChucVuController::class, 'changeStatus']);

        Route::get('/phan-quyen-admin', [PhanQuyenAdminController::class, 'index']);
        Route::post('/phan-quyen-admin', [PhanQuyenAdminController::class, 'store']);
        Route::put('/phan-quyen-admin/{ma_phan_quyen}', [PhanQuyenAdminController::class, 'update']);
        Route::delete('/phan-quyen-admin/{ma_phan_quyen}', [PhanQuyenAdminController::class, 'destroy']);
        
        Route::get('/admin/cau-hinh-ngay', [CauHinhNgayController::class, 'index']);
        Route::post('/admin/cau-hinh-ngay', [CauHinhNgayController::class, 'store']);
        Route::put('/admin/cau-hinh-ngay/{ma_cau_hinh_ngay}', [CauHinhNgayController::class, 'update']);
        Route::delete('/admin/cau-hinh-ngay/{ma_cau_hinh_ngay}', [CauHinhNgayController::class, 'destroy']);
    });

    // 8. Cấu hình AI (108)
    Route::middleware('check.permission:108')->group(function () {
        Route::get('/admin/cau-hinh-ai', [AIConfigController::class, 'getPrompt']);
        Route::post('/admin/cau-hinh-ai', [AIConfigController::class, 'updatePrompt']);
        Route::get('/admin/cau-hinh-ai/api-config', [AIConfigController::class, 'getApiConfig']);
        Route::post('/admin/cau-hinh-ai/api-config', [AIConfigController::class, 'updateApiConfig']);
    });
});
