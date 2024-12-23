<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileInformationController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;

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
    Route::get('/purchase/address/{item_id}', [UserController::class, 'getAddress']);
    Route::patch('/purchase/address/{item_id}', [ProfileInformationController::class, 'postAddress'])->name('user-profile-information.address');
    Route::get('/sell', [ItemController::class, 'getSell']);
    Route::post('/sell', [ItemController::class, 'postSell']);
    Route::post('/purchase/{item_id}', [PaymentController::class, 'postPurchase'])->name('purchase.post');
    Route::post('/stripe/checkout/{item_id}', [PaymentController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/stripe/success', [PaymentController::class, 'success'])->name('stripe.success');
    Route::get('/stripe/cancel', [PaymentController::class, 'cancel'])->name('stripe.cancel');
    // Route::get('/subscription/{item_id}', [PaymentController::class, 'subscription'])->name('stripe.subscription');
    // Route::post('/subscription/afterpay/{item_id}', [PaymentController::class, 'afterpay'])->name('stripe.payment');
});
