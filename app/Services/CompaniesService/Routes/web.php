<?php

use App\Services\CompaniesService\Controllers\CompaniesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CompaniesService - Admin API routes (auth + super_admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth', 'super_admin'])->prefix('admin/api')->name('admin.api.')->group(function () {
    Route::get('companies', [CompaniesController::class, 'index'])->name('companies.index');
    Route::get('companies/create', [CompaniesController::class, 'create'])->name('companies.create');
    Route::post('companies', [CompaniesController::class, 'store'])->name('companies.store');
    Route::get('companies/{company}', [CompaniesController::class, 'show'])->name('companies.show');
    Route::put('companies/{company}', [CompaniesController::class, 'update'])->name('companies.update');
});
