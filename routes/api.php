<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\DiaDiemController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TagDiaDiemController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\PhanQuyenAdminController;
use App\Http\Controllers\XeController;
use App\Http\Controllers\XeKeHoachController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Public routes
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/khach-hang/register', [KhachHangController::class, 'register']);
Route::post('/khach-hang/login', [KhachHangController::class, 'login']);

// Public routes for DiaDiem
Route::get('/dia-diem', [DiaDiemController::class, 'index']);
Route::get('/dia-diem/{maDiaDiem}', [DiaDiemController::class, 'show']);
Route::get('/dia-diem/filter/type/{loai}', [DiaDiemController::class, 'filterByType']);
Route::get('/dia-diem/filter/tag/{maTag}', [DiaDiemController::class, 'filterByTag']);

// Public routes for Tag
Route::get('/tag', [TagController::class, 'index']);
Route::get('/tag/{maTag}', [TagController::class, 'show']);

// Public routes for ChucVu
Route::get('/chuc-vu', [ChucVuController::class, 'index']);

// Public routes for PhanQuyenAdmin
Route::get('/phan-quyen-admin', [PhanQuyenAdminController::class, 'index']);

// Public routes for Xe
Route::get('/xe', [XeController::class, 'index']);

// Public routes for XeKeHoach
Route::get('/xe-ke-hoach', [XeKeHoachController::class, 'index']);

// Public routes for TagDiaDiem
Route::get('/tag-dia-diem', [TagDiaDiemController::class, 'index']);
Route::get('/tag-dia-diem/{maTagDiaDiem}', [TagDiaDiemController::class, 'show']);
Route::get('/tag-dia-diem/location/{maDiaDiem}', [TagDiaDiemController::class, 'getTagsByLocation']);
Route::get('/tag-dia-diem/tag/{maTag}', [TagDiaDiemController::class, 'getLocationsByTag']);

// Admin routes
// Route::middleware(['auth:sanctum'])->group(function () {
// Route::post('/admin/login', [AdminController::class, 'login']);
Route::delete('/admin/{id}', [AdminController::class, 'delete']); //
Route::get('/admin/{id}', [AdminController::class, 'show']); //
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

// Admin routes for ChucVu
Route::get('/chuc-vu/all', [ChucVuController::class, 'indexAll']);
Route::get('/chuc-vu/search', [ChucVuController::class, 'search']);
Route::get('/chuc-vu/{id_chuc_vu}', [ChucVuController::class, 'show']);
Route::post('/chuc-vu', [ChucVuController::class, 'store']);
Route::put('/chuc-vu/{id_chuc_vu}', [ChucVuController::class, 'update']);
Route::patch('/chuc-vu/{id_chuc_vu}/status', [ChucVuController::class, 'changeStatus']);
Route::delete('/chuc-vu/{id_chuc_vu}', [ChucVuController::class, 'destroy']);

// Admin routes for PhanQuyenAdmin
Route::get('/phan-quyen-admin/all', [PhanQuyenAdminController::class, 'indexAll']);
Route::get('/phan-quyen-admin/search', [PhanQuyenAdminController::class, 'search']);
Route::get('/phan-quyen-admin/{id_phan_quyen}', [PhanQuyenAdminController::class, 'show']);
Route::post('/phan-quyen-admin', [PhanQuyenAdminController::class, 'store']);
Route::put('/phan-quyen-admin/{id_phan_quyen}', [PhanQuyenAdminController::class, 'update']);
Route::delete('/phan-quyen-admin/{id_phan_quyen}', [PhanQuyenAdminController::class, 'destroy']);

// Admin routes for Xe
Route::get('/xe/all', [XeController::class, 'indexAll']);
Route::get('/xe/search', [XeController::class, 'search']);
Route::get('/xe/{id_xe}', [XeController::class, 'show']);
Route::post('/xe', [XeController::class, 'store']);
Route::put('/xe/{id_xe}', [XeController::class, 'update']);
Route::patch('/xe/{id_xe}/status', [XeController::class, 'changeStatus']);
Route::delete('/xe/{id_xe}', [XeController::class, 'destroy']);

// Admin routes for XeKeHoach
Route::get('/xe-ke-hoach/all', [XeKeHoachController::class, 'indexAll']);
Route::get('/xe-ke-hoach/search', [XeKeHoachController::class, 'search']);
Route::get('/xe-ke-hoach/{id_xe_ke_hoach}', [XeKeHoachController::class, 'show']);
Route::post('/xe-ke-hoach', [XeKeHoachController::class, 'store']);
Route::put('/xe-ke-hoach/{id_xe_ke_hoach}', [XeKeHoachController::class, 'update']);
Route::delete('/xe-ke-hoach/{id_xe_ke_hoach}', [XeKeHoachController::class, 'destroy']);

Route::post('/tag-dia-diem', [TagDiaDiemController::class, 'store']);
Route::put('/tag-dia-diem/{maTagDiaDiem}', [TagDiaDiemController::class, 'update']);
Route::delete('/tag-dia-diem/{maTagDiaDiem}', [TagDiaDiemController::class, 'destroy']);
Route::put('/admins', [AdminController::class, 'updateAll']);
Route::get('/admins/search', [AdminController::class, 'search']);
Route::put('/admin/password', [AdminController::class, 'changePassword']);
Route::post('/admin/store', [AdminController::class, 'store']);
Route::put('/admin/{id}', [AdminController::class, 'update']);
Route::patch('/admin/{id}/status', [AdminController::class, 'changeStatus']);
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
