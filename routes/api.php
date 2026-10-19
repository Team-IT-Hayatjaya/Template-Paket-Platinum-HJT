<?php

use App\Http\Controllers\Api\DatasetApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Website Resmi Instansi Pemerintah (Paket Platinum)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    Route::get('/datasets', [DatasetApiController::class, 'index'])->name('api.v1.datasets.index');
    Route::get('/datasets/{slug}', [DatasetApiController::class, 'show'])->name('api.v1.datasets.show');
});

// WhatsApp Bot Webhook & Simulator Mode Uji (Paket Platinum)
use App\Http\Controllers\WhatsAppWebhookController;

Route::prefix('v1/whatsapp')->group(function () {
    Route::get('/webhook', [WhatsAppWebhookController::class, 'verify'])->name('api.whatsapp.verify');
    Route::post('/webhook', [WhatsAppWebhookController::class, 'receive'])->name('api.whatsapp.receive');
    Route::post('/test-simulate', [WhatsAppWebhookController::class, 'testSimulate'])->name('api.whatsapp.simulate');
});
