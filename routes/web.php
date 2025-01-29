<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/pay/easymoney', [PaymentController::class, 'payWithEasyMoney']);
Route::post('/process', [PaymentController::class, 'processing'])->name('process');