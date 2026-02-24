# Modular Services

Each service (e.g. WhatsAppService) is self-contained under `app/Services/{ServiceName}/`:

- **Controllers** – HTTP handlers
- **Models** – Eloquent models (or use `app/Models` and reference from service)
- **Jobs** – queued tasks
- **Events** – domain events
- **Listeners** – react to events
- **Repositories** – data access
- **Helpers** – static utilities
- **Routes/** – `web.php` (or `api.php`) loaded by the service’s provider

Register each service in `bootstrap/providers.php` and create a dedicated `ServiceProvider` that:

1. Loads the service routes
2. Optionally registers bindings and views

Adding a new service (e.g. EmailService, SMSService) does not require changes inside WhatsAppService.
