<?php

use App\Services\PaymentsService\Controllers\PaymentsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PaymentsService - Admin API routes (auth + super_admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth', 'super_admin'])->prefix('admin/api')->name('admin.api.')->group(function () {
    Route::get('payments', [PaymentsController::class, 'index'])->name('payments.index');
    Route::post('payments/{payment}/mark-paid', [PaymentsController::class, 'markPaid'])->name('payments.mark-paid');
});
