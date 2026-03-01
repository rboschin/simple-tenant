<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Resolvers;

use Rboschin\SimpleTenant\Models\Tenant;

// use Rboschin\SimpleTenant\Models\TenantPath;

class PathResolver
{
    /**
     * Risolve un tenant dal primo segmento del path URL.
     *
     * Es: dominio.com/carlo → cerca "carlo" in tenant_paths.
     */
    public function resolve(string $pathSegment): ?Tenant
    {
        $pathSegment = trim($pathSegment, '/');

        if (empty($pathSegment)) {
            return null;
        }

        $tenant = Tenant::where('path', $pathSegment)->first();

        if ($tenant && $tenant->is_active) {
            return $tenant;
        }

        return null;
    }
}