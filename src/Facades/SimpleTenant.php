<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Facades;

use Rboschin\SimpleTenant\TenantContext;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void set(\Rboschin\SimpleTenant\Models\Tenant $tenant)
 * @method static array|null get()
 * @method static \Rboschin\SimpleTenant\Models\Tenant|null tenant()
 * @method static string|null uuid()
 * @method static string|null name()
 * @method static bool check()
 * @method static void forget()
 *
 * @see \Rboschin\SimpleTenant\TenantContext
 */
class SimpleTenant extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TenantContext::class;
    }
}