<?php
 
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
 
    Route::get('/auth/register/{id?}', App\Livewire\Auth\Register::class)->name('register'); 
    Route::get('/auth/login', App\Livewire\Auth\Login::class)->name('login'); 

    Route::get('/auth/forgot-password', App\Livewire\Auth\ForgotPassword::class)->name('password.request');
    Route::get('/auth/reset-password/{token}', App\Livewire\Auth\ResetPassword::class)->name('password.reset');
 
});


Route::middleware('auth')->group(function () {
    Route::get('logout', [App\Livewire\Auth\Login::class, 'logout'])->name('logout');
  });
  