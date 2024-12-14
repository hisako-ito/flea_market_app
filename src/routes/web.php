<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;

Route::get('/', [ItemController::class, 'index']);
Route::get('/item/{item_id}', [ItemController::class, 'getDetail']);

// Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::get('/register/add', [UserController::class, 'showAddProfileForm'])->name('profile.add');
    Route::post('/register/add', [UserController::class, 'addProfile'])
        ->name('profile.add.save')->name('profile.add.save'); ;
    Route::get('/purchase/{item_id}', [ItemController::class, 'getPurchase']);
    Route::post('/purchase/{item_id}', [ItemController::class, 'postPurchase']);
    Route::get('/mypage', [UserController::class, 'show']);
    Route::get('/mypage/profile', [UserController::class, 'edit']);
    Route::patch('/mypage/profile', [ProfileInformationController::class, 'update'])
        ->name('user-profile-information.update');
});
