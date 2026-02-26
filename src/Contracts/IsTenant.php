<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Contracts;

interface IsTenant
{
    /**
     * Get the tenant's unique identifier (UUID).
     */
    public function getTenantUuid(): string;

    /**
     * Get the tenant's name or business name.
     */
    public function getTenantName(): string;

    /**
     * Get the data that should be stored in the session.
     */
    public function toSessionArray(): array;
}