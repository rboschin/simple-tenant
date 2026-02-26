<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Middleware;

use Rboschin\SimpleTenant\Exceptions\TenantNotFoundException;
use Rboschin\SimpleTenant\Resolvers\PathResolver;
use Rboschin\SimpleTenant\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenantByPath
{
    public function __construct(
        protected TenantContext $context,
        protected PathResolver $pathResolver,
        )
    {
    }

    /**
     * Identifica il tenant dal path URL.
     *
     * Questo middleware viene usato sulla route fallback.
     * Si attiva solo quando nessun'altra route ha corrisposto.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se il tenant è già identificato, procedi
        if ($this->context->check()) {
            return $next($request);
        }

        $pathSegment = $request->route('tenantPath');

        if (!$pathSegment) {
            throw new TenantNotFoundException();
        }

        $tenant = $this->pathResolver->resolve($pathSegment);

        if (!$tenant) {
            throw new TenantNotFoundException();
        }

        // Salva il tenant nel contesto (sessione)
        $this->context->set($tenant);

        return $next($request);
    }
}