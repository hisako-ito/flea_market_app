<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ProfileInformationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;

Route::get('/', [ItemController::class, 'index'])->name('item.list');
Route::get('/item/{item_id}', [ItemController::class, 'getDetail'])->name('item.detail');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout']);

Route::post('/stripe/check-payment-status', [PaymentController::class, 'checkPaymentStatus'])->name('stripe.check_payment_status');

Route::middleware('auth')->group(function () {
    Route::get('/register/add', [UserController::class, 'add']);
    Route::patch('/register/add', [ProfileInformationController::class, 'update'])->name('user-profile-information.register');
});

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['guest', 'signed'])
    ->name('verification.verify');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/item/{item_id}/favorite', [FavoriteController::class, 'favorite'])->name('favorite');
    Route::post('/item/{item_id}/comment', [CommentController::class, 'storeComment'])->name('item.comments.store');

    Route::get('/mypage', [UserController::class, 'myPageShow'])->name('mypage');
    Route::get('/mypage/profile', [UserController::class, 'edit']);
    Route::patch('/mypage/profile', [ProfileInformationController::class, 'update'])->name('user-profile-information.profile');

    Route::get('/mypage/items/{item_id}/chat', [ChatController::class, 'chatShow'])->name('chat.show');
    Route::post('/chat/message', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/messages/{message_id}/edit', [ChatController::class, 'edit'])->name('chat.edit');
    Route::put('/messages/{message_is}', [ChatController::class, 'update'])->name('chat.update');
    Route::delete('/messages/{message_id}', [ChatController::class, 'destroy'])->name('chat.destroy');

    Route::get('/purchase/{item_id}', [ItemController::class, 'getPurchase'])->name('purchase.get');
    Route::post('/purchase/{item_id}', [PaymentController::class, 'postPurchase'])->name('purchase.post');
    Route::get('/purchase/address/{item_id}', [UserController::class, 'getAddress']);
    Route::patch('/purchase/address/{item_id}', [ProfileInformationController::class, 'postAddress'])->name('user-profile-information.address');

    Route::get('/stripe/cancel', [PaymentController::class, 'cancel'])->name('stripe.cancel');
    Route::get('/stripe/waiting-for-payment', [PaymentController::class, 'waitingForPayment'])->name('stripe.waiting_for_payment');

    Route::get('/sell', [ItemController::class, 'getSell']);
    Route::post('/sell', [ItemController::class, 'postSell']);
});
