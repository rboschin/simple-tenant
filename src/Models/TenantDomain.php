<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class TenantDomain extends Model
{
    protected $table = 'st_tenant_domains';

    protected $fillable = [
        'uuid',
        'tenant_uuid',
        'domain',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self$domain) {
            if (empty($domain->uuid)) {
                $domain->uuid = Uuid::uuid4()->toString();
            }
        });
    }

    /**
     * Tenant a cui appartiene questo dominio.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class , 'tenant_uuid', 'uuid');
    }
}