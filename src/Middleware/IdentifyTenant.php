<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Middleware;

use Rboschin\SimpleTenant\Exceptions\TenantNotFoundException;
use Rboschin\SimpleTenant\Resolvers\DomainResolver;
use Rboschin\SimpleTenant\Resolvers\PathResolver;
use Rboschin\SimpleTenant\Resolvers\RequestDataResolver;
use Rboschin\SimpleTenant\Resolvers\SingleTenantResolver;
use Rboschin\SimpleTenant\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function __construct(
        protected TenantContext $context,
        protected DomainResolver $domainResolver,
        protected SingleTenantResolver $singleTenantResolver,
        protected RequestDataResolver $requestDataResolver,
        protected PathResolver $pathResolver,
        )
    {
    }

    /**
     * Identifica il tenant dalla richiesta HTTP.
     * Arriva qui solo con path = singola variabile (es: acme)
     *  => $request->route('tenantPath') valorizzata
     *
     * Ordine di risoluzione:
     * 1. Single-tenant mode (se configurato in .env)
     * 2. Request Data resolution (Form field, GET param, etc.)
     * 3. Domain-based resolution (dominio / sottodominio)
     * 4. Path-based resolution (path = singola variabile)
     * 5. 404 se non trovato
     */
    public function handle(Request $request, Closure $next): Response
    {
        // \Log::info('Fallback route hit', ['url' => $request->url()]);

        // Se autenticato, continua
        if (\Auth::check()) {
            return $next($request);
        }

        // Se il tenant è già identificato nel contesto, continua
        if ($this->context->check()) {
            return $next($request);
        }

        $tenant = null;

        // 1. Single-tenant mode
        if (config('simpletenant.single_tenant_uuid')) {
            $tenant = $this->singleTenantResolver->resolve();
        }

        // 2. Request Data resolution (Form field, GET param, etc.)
        if (!$tenant && $requestKey = config('simpletenant.identification_request_key')) {
            if ($pathSegment = $request->input($requestKey)) {
                $tenant = $this->requestDataResolver->resolve($pathSegment);
            }
        }

        // 3. Domain-based resolution
        if (!$tenant && config('simpletenant.enable_domain_resolution', true)) {
            $host = $request->getHost();

            $centralDomains = config('simpletenant.central_domains', []);
            if (!in_array($host, $centralDomains)) {
                $tenant = $this->domainResolver->resolve($host);
            }
        }

        // 4. Path-based resolution
        $pathSegment = $request->path();;
        // $pathSegment = $request->route('tenantPath');
        if ($pathSegment) {
            $tenant = $this->pathResolver->resolve($pathSegment);
        }

        if (!$tenant) {
            $host = $request->getHost();
            $centralDomains = config('simpletenant.central_domains', []);

            // Se è un dominio centrale, lascia passare
            if (in_array($host, $centralDomains)) {
                return $next($request);
            }

            // Se siamo sulla route fallback e non troviamo il tenant, 404
            if ($request->route() && $request->route()->getName() === 'simpletenant.fallback') {
                abort(404, 'Tenant not found');
            }

            // Altrimenti continua - verrà gestito dalla auth
            return $next($request);
        }

        // Salva il tenant nel contesto (sessione)
        $this->context->set($tenant);

        // Se utente non autenticato, redirect al login
        if (!\Auth::check()) {
            return redirect()->guest(route('login'));
        }

        // Continua la richiesta
        return $next($request);
    }
}