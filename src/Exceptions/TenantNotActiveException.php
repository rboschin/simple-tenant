<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Exceptions;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TenantNotActiveException extends AccessDeniedHttpException
{
    public function __construct(string $message = 'Tenant is not active.')
    {
        parent::__construct($message);
    }
}