<?php

use App\Services\WhatsAppService\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WhatsApp Service Routes (modular)
|--------------------------------------------------------------------------
*/

Route::prefix('webhook/whatsapp')->name('webhook.whatsapp.')->group(function () {
    Route::get('/', [WebhookController::class, 'verify'])->name('verify');
    Route::post('/', [WebhookController::class, 'handle'])->name('handle');
});
