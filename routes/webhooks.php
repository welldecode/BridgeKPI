<?php

use App\Http\Controllers\Gateways\MercadoPagoController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;


Route::prefix('webhooks')->name('webhooks.')->group(function () { 
    Route::post('/mercadopago', [PaymentController::class, 'handleWebhook']); 
});