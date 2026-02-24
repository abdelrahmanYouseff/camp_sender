<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class OwnerDashboardServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        $path = base_path('app/Services/OwnerDashboard/Routes/web.php');
        if (file_exists($path)) {
            Route::middleware('web')->group($path);
        }
    }
}
