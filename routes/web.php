<?php

use App\Http\Controllers\Admin\BalancoPatrimonialController;
use App\Http\Controllers\Admin\Dados\DadosBPController;
use App\Http\Controllers\Admin\DreController;
use App\Livewire\Admin\Dados\BalancoPatrimonial;
use Illuminate\Support\Facades\Route;
 

    Route::redirect('/', 'admin/');
Route::prefix('admin')->middleware(['auth', 'verified'])->name('admin.')->group(function () {

Route::get('/', App\Livewire\Admin\Dashboard::class)->name('index');
    Route::prefix('dados')->middleware(['auth', 'verified', 'plan_subscription'])->name('dados.')->group(function () {
        Route::get('/balanco_patrimonial', App\Livewire\Admin\Dados\BalancoPatrimonial::class)->name('balanco_patrimonial');
        Route::get('/dre', App\Livewire\Admin\Dados\Dre::class)->name('dre');
    });
    Route::prefix('analise')->middleware(['auth', 'verified', 'plan_subscription'])->name('analise.')->group(function () {
        Route::get('/analise_vh', App\Livewire\Admin\Analise\AnaliseVH::class)->name('analise_vh');
        Route::get('/indicadores', App\Livewire\Admin\Analise\Indicadores::class)->name('indicadores');
        Route::get('/relatorio', App\Livewire\Admin\Analise\Relatorio::class)->name('relatorio');
    });

    Route::prefix('demo')->middleware(['auth', 'verified', 'plan_subscription'])->name('demo.')->group(function () {
        Route::get('/balanco_patrimonial', App\Livewire\Admin\Demo\BP::class)->name('bp');
        Route::get('/dre', App\Livewire\Admin\Demo\Dre::class)->name('dre');


        Route::post('/balanco_patrimonial', [DadosBPController::class, 'store'])->name('balanco_patrimonial');
    });
});

Route::prefix('profile')->middleware(['auth', 'verified', 'plan_subscription'])->name('user.')->group(function () {
    Route::get('/user', App\Livewire\Profile\User::class)->name('user');
    Route::get('/business', App\Livewire\Profile\Business::class)->name('business');
    Route::get('/signature', App\Livewire\Profile\Plans::class)->name('signature');
    Route::get('/invoices', App\Livewire\Profile\Invoices::class)->name('invoices');
    Route::get('/invoices/{id}', App\Livewire\Profile\InvoicesVIew::class)->name('invoiceview');

});

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', App\Livewire\Components\Cart\CartPage::class)->name('index');
});

Route::prefix('payment')->name('pay.')->group(function () {
    Route::get('/checkout', App\Livewire\Payment\Checkout::class)->name('checkout');
    Route::get('/order/{id}/{payment}', App\Livewire\Payment\Orders::class)->name('orders');
});

Route::prefix('plans')->name('plans.')->group(function () {
    Route::get('/plan_payment', App\Livewire\Plans\Payment::class)->name('plan_payment');
    Route::get('/list', App\Livewire\Plans\Index::class)->name('index');
});


Route::prefix('dados')->middleware(['auth', 'verified'])->name('dados.')->group(function () {
    Route::post('/balanco_patrimonial', [BalancoPatrimonialController::class, 'store'])->name('balando_patrimonial');
    Route::post('/dre', [DreController::class, 'store'])->name('dre');
});

require __DIR__ . '/webhooks.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/payments.php';
