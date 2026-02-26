<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant;

use Rboschin\SimpleTenant\Middleware\IdentifyTenant;
use Rboschin\SimpleTenant\Middleware\IdentifyTenantByPath;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SimpleTenantServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/simpletenant.php', 'simpletenant');

        // TenantContext come singleton per mantenere lo stato durante la richiesta
        $this->app->singleton(TenantContext::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishConfig();
        $this->publishMigrations();
        $this->registerMiddlewareAliases();
        $this->registerPathFallbackRoute();
    }

    /**
     * Pubblica il file di configurazione.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/simpletenant.php' => config_path('simpletenant.php'),
        ], 'simpletenant-config');
    }

    /**
     * Pubblica le migrations base e le stub migrations.
     */
    protected function publishMigrations(): void
    {
        // Migrations base (tenants, tenant_domains, tenant_paths)
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'simpletenant-migrations');

        // Stub migrations (aggiunta tenant_uuid a tabelle esistenti)
        $this->publishes([
            __DIR__ . '/../database/stubs/' => database_path('migrations'),
        ], 'simpletenant-stubs');

        // Carica automaticamente le migrations base del package
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Registra gli alias per i middleware.
     */
    protected function registerMiddlewareAliases(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('simpletenant.identify', IdentifyTenant::class);
        $router->aliasMiddleware('simpletenant.path', IdentifyTenantByPath::class);
    }

    /**
     * Registra la route catch-all per la risoluzione via path.
     *
     * Questa route viene registrata DOPO il boot dell'applicazione,
     * così che tutte le altre route definite dall'utente abbiano la precedenza.
     * Si attiva solo quando nessun'altra route ha corrisposto.
     */
    protected function registerPathFallbackRoute(): void
    {
        if (!config('simpletenant.enable_path_resolution', true)) {
            return;
        }

        // Registra la route catch-all dopo che tutte le route dell'app sono caricate
        $this->app->booted(function () {
            Route::middleware(['web', 'simpletenant.path'])
                ->any('/{tenantPath}', function () {
                $context = app(TenantContext::class);

                if ($context->check()) {
                    return response()->json([
                    'tenant' => $context->get(),
                    'message' => 'Tenant identified via path.',
                    ]);
                }

                abort(404);
            }
            )
                ->where('tenantPath', '[a-zA-Z0-9\-\_]+')
                ->name('simpletenant.path.fallback');
        });
    }
}