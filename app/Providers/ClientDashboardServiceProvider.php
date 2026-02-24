<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ClientDashboardServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        $path = base_path('app/Services/ClientDashboardService/Routes/web.php');
        if (file_exists($path)) {
            Route::middleware('web')->group($path);
        }
    }
}
