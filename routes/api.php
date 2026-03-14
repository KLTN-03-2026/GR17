<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\DiaDiemController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TagDiaDiemController;

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
