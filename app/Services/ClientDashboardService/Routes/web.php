<?php

use App\Services\ClientDashboardService\Controllers\AccountController;
use App\Services\ClientDashboardService\Controllers\AuthController;
use App\Services\ClientDashboardService\Controllers\AutomationSettingsController;
use App\Services\ClientDashboardService\Controllers\EmployeesController;
use App\Services\ClientDashboardService\Controllers\InboxController;
use App\Services\ClientDashboardService\Controllers\LeadsController;
use App\Services\ClientDashboardService\Controllers\StatisticsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client Dashboard - Web routes (SPA shell + API)
|--------------------------------------------------------------------------
*/

Route::prefix('client')->name('client.')->group(function () {
    Route::get('login', function () {
        if (Auth::check() && Auth::user()->company_id && Auth::user()->role !== 'super_admin') {
            return redirect()->route('client.index');
        }
        return view('client');
    })->name('login')->middleware('guest');

    Route::post('login', [AuthController::class, 'login'])->name('login.attempt')->middleware('guest');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    Route::middleware(['auth', 'client_user'])->group(function () {
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('user', [AuthController::class, 'user'])->name('user');
            Route::get('inbox', [InboxController::class, 'index'])->name('inbox.index');
            Route::post('inbox/simulate-incoming', [InboxController::class, 'simulateIncoming'])->name('inbox.simulate-incoming');
            Route::get('inbox/{id}', [InboxController::class, 'show'])->name('inbox.show');
            Route::post('inbox/{id}/close', [InboxController::class, 'close'])->name('inbox.close');
            Route::post('inbox/{id}/reply', [InboxController::class, 'reply'])->name('inbox.reply');
            Route::get('leads', [LeadsController::class, 'index'])->name('leads.index');
            Route::post('leads/sync-from-conversations', [LeadsController::class, 'syncFromConversations'])->name('leads.sync-from-conversations');
            Route::post('leads/{id}/assign', [LeadsController::class, 'assign'])->name('leads.assign');
            Route::patch('leads/{id}/interest', [LeadsController::class, 'updateInterest'])->name('leads.update-interest');
            Route::get('employees', [EmployeesController::class, 'index'])->name('employees.index');
            Route::post('employees', [EmployeesController::class, 'store'])->name('employees.store');
            Route::post('employees/{id}/update', [EmployeesController::class, 'update'])->name('employees.update');
            Route::post('employees/{id}/toggle-status', [EmployeesController::class, 'toggleStatus'])->name('employees.toggle-status');
            Route::get('automation', [AutomationSettingsController::class, 'show'])->name('automation.show');
            Route::post('automation/update', [AutomationSettingsController::class, 'update'])->name('automation.update');
            Route::get('dashboard', [StatisticsController::class, 'dashboard'])->name('dashboard');
            Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics.index');
            Route::get('account', [AccountController::class, 'show'])->name('account.show');
            Route::post('account/update', [AccountController::class, 'update'])->name('account.update');
        });

        Route::get('/', fn () => view('client'))->name('index');
        Route::get('{path}', fn () => view('client'))->where('path', '^(?!api|login).*')->name('spa');
    });
});
