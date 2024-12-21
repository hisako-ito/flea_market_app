<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileInformationController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;

Route::get('/', [ItemController::class, 'index']);
Route::get('/item/{item_id}', [ItemController::class, 'getDetail']);

// Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::get('/register/add', [UserController::class, 'add']);
    Route::patch('/register/add', [ProfileInformationController::class, 'update'])->name('user-profile-information.register');
    Route::get('/mypage', [UserController::class, 'show']);
    Route::get('/mypage/profile', [UserController::class, 'edit']);
    Route::patch('/mypage/profile', [ProfileInformationController::class, 'update'])
        ->name('user-profile-information.profile');
    Route::get('/purchase/{item_id}', [ItemController::class, 'getPurchase']);
    Route::post('/purchase/{item_id}', [ItemController::class, 'postPurchase']);
    Route::get('/purchase/address/{item_id}', [UserController::class, 'getAddress']);
    Route::patch('/purchase/address/{item_id}', [ProfileInformationController::class, 'postAddress'])->name('user-profile-information.address');
    Route::get('/sell', [ItemController::class, 'getSell']);
    Route::post('/sell', [ItemController::class, 'postSell']);
});
