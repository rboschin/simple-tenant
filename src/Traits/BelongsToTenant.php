<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Traits;

use Rboschin\SimpleTenant\Models\Tenant;
use Rboschin\SimpleTenant\Scopes\TenantScope;
use Rboschin\SimpleTenant\TenantContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait per modelli che appartengono a un tenant.
 *
 * Aggiunge:
 * - Relazione tenant()
 * - Global scope per il filtraggio automatico
 * - Auto-impostazione di tenant_uuid alla creazione
 *
 * Usare nei modelli: use BelongsToTenant;
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Registra il global scope per il filtraggio automatico
        static::addGlobalScope(new TenantScope());

        // Auto-imposta tenant_uuid quando un modello viene creato
        static::creating(function ($model) {
            $column = config('simpletenant.tenant_uuid_column', 'tenant_uuid');

            if (empty($model->{ $column})) {
                /** @var TenantContext $context */
                $context = app(TenantContext::class);

                if ($context->check()) {
                    $model->{ $column} = $context->uuid();
                }
            }
        });
    }

    /**
     * Relazione con il tenant proprietario.
     */
    public function tenant(): BelongsTo
    {
        $column = config('simpletenant.tenant_uuid_column', 'tenant_uuid');

        return $this->belongsTo(
            config('simpletenant.tenant_model', Tenant::class),
            $column,
            'uuid'
        );
    }

    /**
     * Rimuovi il global scope TenantScope per questa query.
     */
    public static function withoutTenantScope(): \Illuminate\Database\Eloquent\Builder
    {
        return static::withoutGlobalScope(TenantScope::class);
    }
}