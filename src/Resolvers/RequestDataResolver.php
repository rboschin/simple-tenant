<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Resolvers;

use Rboschin\SimpleTenant\Models\Tenant;

// use Rboschin\SimpleTenant\Models\TenantPath;

class RequestDataResolver
{
    /**
     * Risolve un tenant da un parametro presente nella richiesta (POST o GET).
     *
     * Es: un campo 'tenant_path' in un form di login.
     */
    public function resolve(string $pathSegment): ?Tenant
    {
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