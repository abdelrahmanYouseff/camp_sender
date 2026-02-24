<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CompaniesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        $path = base_path('app/Services/CompaniesService/Routes/web.php');
        if (file_exists($path)) {
            Route::group([], $path);
        }
    }
}
