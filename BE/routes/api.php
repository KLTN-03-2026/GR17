<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NhomController;

Route::prefix('nhom')->group(function () {
    Route::get('/', [NhomController::class, 'index']);
    Route::get('/{id}', [NhomController::class, 'show']);
});
