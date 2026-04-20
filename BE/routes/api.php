<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\ChiTietTourController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('tour', TourController::class);

Route::prefix('admin')->group(function () {
    Route::apiResource('hoa-don', HoaDonController::class);
});

Route::apiResource('chi-tiet-tour', ChiTietTourController::class);

Route::prefix('nhom')->group(function () {
    // Day 1: bootstrap route for group module before controller CRUD.
    Route::get('/', function () {
        return response()->json([
            'success' => true,
            'message' => 'Day 1 group API route is ready.',
        ]);
    });
});
