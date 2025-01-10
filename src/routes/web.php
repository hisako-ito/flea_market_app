<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ProfileInformationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
// use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Middleware\RedirectIfAuthenticatedToVerify;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\EmailVerificationRequest;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

Route::get('/', [ItemController::class, 'index'])->name('item.list');
Route::get('/item/{item_id}', [ItemController::class, 'getDetail'])->name('item.detail');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout']);

Route::middleware('auth')->group(function () {
    Route::get('/register/add', [UserController::class, 'add']);
    Route::patch('/register/add', [ProfileInformationController::class, 'update'])->name('user-profile-information.register');
});

Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = User::find($id);

    if (! $user || ! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, '無効な認証リンクです。');
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return redirect('/login')->with('message', 'メールアドレスが確認されました。');
})->middleware(['signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '確認メールを再送信しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/items/{item}/favorite', [FavoriteController::class, 'favorite'])->name('favorite');
    Route::post('/items/{id}/comments', [CommentController::class, 'storeComment'])->name('items.comments.store');

    Route::get('/mypage', [UserController::class, 'show'])->name('mypage');
    Route::get('/mypage/profile', [UserController::class, 'edit']);
    Route::patch('/mypage/profile', [ProfileInformationController::class, 'update'])->name('user-profile-information.profile');

    Route::get('/purchase/{item_id}', [ItemController::class, 'getPurchase'])->name('purchase.get');
    Route::post('/purchase/{item_id}', [PaymentController::class, 'postPurchase'])->name('purchase.post');
    Route::get('/purchase/address/{item_id}', [UserController::class, 'getAddress']);
    Route::patch('/purchase/address/{item_id}', [ProfileInformationController::class, 'postAddress'])->name('user-profile-information.address');

    Route::get('/stripe/cancel', [PaymentController::class, 'cancel'])->name('stripe.cancel');
    Route::get('/stripe/waiting-for-payment', [PaymentController::class, 'waitingForPayment'])->name('stripe.waiting_for_payment');
    Route::post('/stripe/check-payment-status', [PaymentController::class, 'checkPaymentStatus'])->name('stripe.check_payment_status');

    Route::get('/sell', [ItemController::class, 'getSell']);
    Route::post('/sell', [ItemController::class, 'postSell']);
});
