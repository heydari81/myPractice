<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::prefix('auth')->group(function () {
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');
});
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('password/email', [AuthController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [AuthController::class, 'resetPassword']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:jwt');
Route::middleware(['auth:jwt'])->group(function () {
    Route::post('payment', [PaymentController::class,'payment']);
});

Route::middleware(['auth:jwt', 'admin'])->group(function () {
    Route::post('products', [ProductController::class,'store']);
    Route::patch('products/{product}', [ProductController::class,'update']);
    Route::delete('products/{product}', [ProductController::class,'destroy']);
});
Route::get('products', [ProductController::class,'index']);
Route::get('callback', [PaymentController::class,'callback'])->name('callback');
Route::get('products/{product}', [ProductController::class,'show']);
Route::get('cached', function (){
    dd(\Illuminate\Support\Facades\Cache::get('products'));
});
Route::get('/search', [ProductController::class, 'search']);
