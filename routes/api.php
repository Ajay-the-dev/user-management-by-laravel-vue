<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\BatchController;



//users
Route::prefix('users')->group(function () {
        Route::post('/authenticate', [UserController::class, 'login']);
        Route::post('/create', [UserController::class, 'store']);
        Route::get('/', [UserController::class, 'index']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'remove']);
        Route::get('/{id}', [UserController::class, 'resetPassword']);
        Route::post('byParams', [UserController::class, 'filterByParams']);
});

//batches
Route::prefix('batches')->group(function () {
        Route::post('/create', [BatchController::class, 'store']);
        Route::get('/', [BatchController::class, 'index']);
        Route::put('/{id}', [BatchController::class, 'update']);
        Route::delete('/{id}', [BatchController::class, 'remove']);
});