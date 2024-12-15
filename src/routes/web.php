<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileInformationController;
// use Laravel\Fortify\Http\Controllers\ProfileInformationController;

Route::get('/', [ItemController::class, 'index']);
Route::get('/item/{item_id}', [ItemController::class, 'getDetail']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::get('/mypage/profile', [UserController::class, 'edit']);
    Route::patch('/mypage/profile', [ProfileInformationController::class, 'update'])
        ->name('user-profile-information.profile');
    Route::get('/register/add', [UserController::class, 'add']);
    Route::patch('/register/add', [ProfileInformationController::class, 'update'])->name('user-profile-information.register');
    Route::get('/purchase/{item_id}', [ItemController::class, 'getPurchase']);
    Route::post('/purchase/{item_id}', [ItemController::class, 'postPurchase']);
    Route::get('/mypage', [UserController::class, 'show']);
});
