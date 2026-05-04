<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\ChiTietTourController;
use App\Http\Controllers\NhomController;
use App\Http\Controllers\ThanhVienNhomController;
use App\Http\Controllers\DanhGiaKeHoachController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('tour', TourController::class);

Route::prefix('admin')->group(function () {
    Route::patch('hoa-don/{ma_hoa_don}/status', [HoaDonController::class, 'updateStatus']);
    Route::apiResource('hoa-don', HoaDonController::class);
});

Route::apiResource('chi-tiet-tour', ChiTietTourController::class);

Route::prefix('nhom')->group(function () {
    Route::get('/', [NhomController::class, 'index']);
    Route::get('/search', [NhomController::class, 'search']);
    Route::get('/{id}', [NhomController::class, 'show']);
    Route::post('/', [NhomController::class, 'store']);
    Route::put('/{id}', [NhomController::class, 'update']);
    Route::delete('/{id}', [NhomController::class, 'destroy']);
});

Route::prefix('thanh-vien-nhom')->group(function () {
    Route::get('/', [ThanhVienNhomController::class, 'index']);
    Route::get('/search', [ThanhVienNhomController::class, 'search']);
    Route::get('/nhom/{maNhom}', [ThanhVienNhomController::class, 'getByNhom']);
    Route::get('/{id}', [ThanhVienNhomController::class, 'show']);
    Route::post('/', [ThanhVienNhomController::class, 'store']);
    Route::put('/{id}', [ThanhVienNhomController::class, 'update']);
    Route::delete('/{id}', [ThanhVienNhomController::class, 'destroy']);
});

Route::prefix('danh-gia-ke-hoach')->group(function () {
    Route::get('/', [DanhGiaKeHoachController::class, 'index']);
    Route::get('/search', [DanhGiaKeHoachController::class, 'search']);
    Route::get('/summary', [DanhGiaKeHoachController::class, 'summary']);
    Route::get('/dia-diem/{maDiaDiem}', [DanhGiaKeHoachController::class, 'getByDiaDiem']);
    Route::get('/{id}', [DanhGiaKeHoachController::class, 'show']);
    Route::post('/', [DanhGiaKeHoachController::class, 'store']);
    Route::put('/{id}', [DanhGiaKeHoachController::class, 'update']);
    Route::delete('/{id}', [DanhGiaKeHoachController::class, 'destroy']);
});
