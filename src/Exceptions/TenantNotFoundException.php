<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TenantNotFoundException extends NotFoundHttpException
{
    public function __construct(string $message = 'Tenant not found.')
    {
        parent::__construct($message);
    }
}