<?php

use App\Services\OwnerDashboard\Controllers\AuthController;
use App\Services\OwnerDashboard\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Owner Dashboard (Admin) - Web routes (SPA shell + API)
| Company CRUD is in CompaniesService.
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', function () {
        if (Auth::check() && Auth::user()->role === 'super_admin') {
            return redirect()->route('admin.index');
        }
        return view('admin');
    })->name('login')->middleware('guest');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt')->middleware('guest');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    Route::middleware(['auth', 'super_admin'])->group(function () {
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('user', [AuthController::class, 'user'])->name('user');
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        });

        // SPA shell: serve Vue app for all admin UI routes
        Route::get('/', fn () => view('admin'))->name('index');
        Route::get('{path}', fn () => view('admin'))->where('path', '^(?!api|login).*')->name('spa');
    });
});
