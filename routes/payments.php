<?php

use App\Http\Controllers\MPWebHookController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;


Route::prefix('payments')->name('payments.')->group(function () {  

    Route::post('/process_payment', [PaymentController::class, 'payments'])->name('process_payment');
    Route::post('/coupon-add', [PaymentController::class, 'couponStore'])->name('couponAdd');   
}); 