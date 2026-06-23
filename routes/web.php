<?php

use App\Http\Controllers\Menu\DigitalMenuController;
use App\Http\Controllers\Menu\LogoUploadController;
use App\Http\Controllers\Menu\ReceiptPrintController;
use App\Http\Controllers\Menu\ReceiptTestController;
use App\Http\Controllers\Payment\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/language/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'km'])) {
        abort(404);
    }

    session(['locale' => $locale]);

    return redirect()->back();
})->name('language.switch');

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
    Route::post('/admin/upload/menu-logo', LogoUploadController::class);

    Route::get('/admin/receipts/{order}', [ReceiptPrintController::class, 'show'])->name('admin.receipt.show');
    Route::get('/admin/receipts/{order}/pdf', [ReceiptPrintController::class, 'printPdf'])->name('admin.receipt.pdf');

    Route::get('/pos', function () {
        return view('pos.index');
    })->name('pos');

    Route::get('/pos/orders', function () {
        return view('pos.orders');
    })->name('pos.orders');
});
