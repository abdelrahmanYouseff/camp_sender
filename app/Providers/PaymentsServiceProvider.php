<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PaymentsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        $path = base_path('app/Services/PaymentsService/Routes/web.php');
        if (file_exists($path)) {
            Route::group([], $path);
        }
    }
}
