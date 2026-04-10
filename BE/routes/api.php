<?php

use Illuminate\Support\Facades\Route;

Route::prefix('nhom')->group(function () {
    // Day 1: bootstrap route for group module before controller CRUD.
    Route::get('/', function () {
        return response()->json([
            'success' => true,
            'message' => 'Day 1 group API route is ready.',
        ]);
    });
});
