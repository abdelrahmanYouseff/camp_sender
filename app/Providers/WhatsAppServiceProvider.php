<?php

namespace App\Providers;

use App\Services\WhatsAppService\Events\WhatsAppMessageReceived;
use App\Services\WhatsAppService\Listeners\CreateLeadFromIncomingMessageListener;
use App\Services\WhatsAppService\Listeners\SendAutoReplyListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WhatsAppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerBindings();
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerViews();
        Event::listen(WhatsAppMessageReceived::class, SendAutoReplyListener::class);
        Event::listen(WhatsAppMessageReceived::class, CreateLeadFromIncomingMessageListener::class);
    }

    protected function registerRoutes(): void
    {
        $routesPath = base_path('app/Services/WhatsAppService/Routes/web.php');
        if (file_exists($routesPath)) {
            Route::middleware('web')->group($routesPath);
        }
    }

    protected function registerViews(): void
    {
        $viewsPath = resource_path('views/services/whatsapp');
        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, 'whatsapp');
        }
    }

    protected function registerBindings(): void
    {
        // Repository bindings (optional – use interfaces for cleaner architecture)
        // $this->app->bind(ConversationRepositoryInterface::class, ConversationRepository::class);
    }
}
