<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Middleware;

use Rboschin\SimpleTenant\Exceptions\TenantNotFoundException;
use Rboschin\SimpleTenant\Resolvers\DomainResolver;
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
        )
    {
    }

    /**
     * Identifica il tenant dalla richiesta HTTP.
     *
     * Ordine di risoluzione:
     * 1. Single-tenant mode (se configurato in .env)
     * 2. Request Data resolution (Form field, GET param, etc.)
     * 3. Domain-based resolution (dominio / sottodominio)
     * 4. 404 se non trovato
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If authenticated continue
        if (\Auth::check()) {
            return $next($request);
        }
        dd($request->route('tenantPath'));
        // Se il tenant è già identificato in sessione, usalo
        if ($this->context->check()) {
            return redirect()->route('login');
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

            // Controlla che non sia un dominio centrale
            $centralDomains = config('simpletenant.central_domains', []);
            if (!in_array($host, $centralDomains)) {
                $tenant = $this->domainResolver->resolve($host);
            }
        }

        $pathSegment = $request->route('tenantPath');

        if ($pathSegment) {
            $tenant = $this->pathResolver->resolve($pathSegment);
            if ($tenant) {
                // Salva il tenant nel contesto (sessione)
                $this->context->set($tenant);
            }
        }


        if (!$tenant) {
            $host = $request->getHost();
            $centralDomains = config('simpletenant.central_domains', []);

            if (in_array($host, $centralDomains)) {
                return $next($request);
            }

            #            throw new TenantNotFoundException();
            return $next($request);
        }

        // Salva il tenant nel contesto (sessione)
        $this->context->set($tenant);

        return redirect()->route('login');
    }
}