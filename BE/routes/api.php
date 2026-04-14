<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\ChiTietTourController;
use App\Http\Controllers\DichVuDiaDiemController;
use App\Http\Controllers\NhomController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('tour', TourController::class);

Route::prefix('admin')->group(function () {
    Route::apiResource('hoa-don', HoaDonController::class);
});

Route::apiResource('chi-tiet-tour', ChiTietTourController::class);

Route::apiResource('dich-vu-dia-diem', DichVuDiaDiemController::class);
Route::get('dich-vu-dia-diem-all', [DichVuDiaDiemController::class, 'index']);

Route::prefix('nhom')->group(function () {
    Route::get('/', [NhomController::class, 'index']);
    Route::get('/{id}', [NhomController::class, 'show']);
    Route::post('/', [NhomController::class, 'store']);
});
