<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MyPageController;


Route::get('/', [ItemController::class, 'index']);
Route::get('/item/{item_id}', [ItemController::class, 'getDetail']);

Route::middleware('auth')->group(function () {
    // Route::get('/', [MyPageController::class, 'index']);
    // Route::get('/item/{item_id}', [MyPageController::class, 'getDetail']);
    });
