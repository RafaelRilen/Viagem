<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::get('/notifications', function (Request $request) {
    return $request->user()->notifications;
});

Route::middleware('jwt.auth')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{orderId}', [OrderController::class, 'show']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel']);
});
