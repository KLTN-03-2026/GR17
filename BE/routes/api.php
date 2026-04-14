<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\ChiTietTourController;
<<<<<<< HEAD
use App\Http\Controllers\DichVuDiaDiemController;
=======

>>>>>>> 367e6940a2e6ebc1215da8dda8df04e1d1ff7107

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('tour', TourController::class);

Route::prefix('admin')->group(function () {
    Route::apiResource('hoa-don', HoaDonController::class);
});

Route::apiResource('chi-tiet-tour', ChiTietTourController::class);
=======
Route::prefix('nhom')->group(function () {
    Route::get('/', [NhomController::class, 'index']);
    Route::get('/{id}', [NhomController::class, 'show']);
    Route::post('/', [NhomController::class, 'store']);
});
>>>>>>> 367e6940a2e6ebc1215da8dda8df04e1d1ff7107
