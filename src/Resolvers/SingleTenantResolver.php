<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Resolvers;

use Rboschin\SimpleTenant\Models\Tenant;

class SingleTenantResolver
{
    /**
     * Risolve il tenant dalla configurazione single-tenant.
     * Usa il valore SIMPLETENANT_SINGLE_TENANT_UUID dal file .env.
     */
    public function resolve(): ?Tenant
    {
        $uuid = config('simpletenant.single_tenant_uuid');

        if (empty($uuid)) {
            return null;
        }

        return Tenant::where('uuid', $uuid)
            ->where('is_active', true)
            ->first();
    }
}