<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Resolvers;

use Rboschin\SimpleTenant\Models\Tenant;
use Rboschin\SimpleTenant\Models\TenantDomain;
use Illuminate\Support\Str;

class DomainResolver
{
    /**
     * Risolve un tenant dal dominio della richiesta HTTP.
     *
     * Normalizza www.dominio.com → dominio.com.
     * Sottodomini diversi da www (es. marco.dominio.com) sono tenant separati.
     */
    public function resolve(string $host): ?Tenant
    {
        $domain = $this->normalizeDomain($host);

        $tenantDomain = TenantDomain::where('domain', $domain)->first();

        if (!$tenantDomain) {
            return null;
        }

        $tenant = $tenantDomain->tenant;

        if ($tenant && $tenant->is_active) {
            return $tenant;
        }

        return null;
    }

    /**
     * Normalizza il dominio:
     * - www.dominio.com → dominio.com
     * - marco.dominio.com → marco.dominio.com (invariato)
     */
    protected function normalizeDomain(string $host): string
    {
        $host = Str::lower($host);

        if (Str::startsWith($host, 'www.')) {
            return Str::substr($host, 4);
        }

        return $host;
    }
}