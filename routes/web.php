<?php

use App\Http\Controllers\Menu\DigitalMenuController;
use App\Http\Controllers\Menu\ReceiptTestController;
use App\Http\Controllers\Payment\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/menu', DigitalMenuController::class)->name('menu');

Route::get('/admin/test-receipt', ReceiptTestController::class)
    ->name('admin.test-receipt')
    ->middleware('auth');

// Payment webhooks (no auth required)
Route::post('/api/v1/payments/webhook', [PaymentWebhookController::class, 'handle'])
    ->name('payments.webhook');
Route::get('/api/v1/payments/status/{orderNumber}', [PaymentWebhookController::class, 'checkStatus'])
    ->name('payments.status');

Route::middleware('auth')->group(function () {
    Route::get('/pos', function () {
        return view('pos.index');
    })->name('pos');

    Route::get('/pos/orders', function () {
        return view('pos.orders');
    })->name('pos.orders');
});
