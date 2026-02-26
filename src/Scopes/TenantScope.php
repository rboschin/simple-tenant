<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Scopes;

use Rboschin\SimpleTenant\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Applica il filtro tenant a tutte le query del modello.
     * Aggiunge automaticamente WHERE tenant_uuid = ? usando il tenant corrente.
     */
    public function apply(Builder $builder, Model $model): void
    {
        /** @var TenantContext $context */
        $context = app(TenantContext::class);

        if ($context->check()) {
            $column = $model->getTable() . '.' . config('simpletenant.tenant_uuid_column', 'tenant_uuid');
            $builder->where($column, $context->uuid());
        }
    }
}