<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

use Rboschin\SimpleTenant\Contracts\IsTenant;

class Tenant extends Model implements IsTenant
{
    use SoftDeletes;

    protected $table = 'tenants';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'email',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self$tenant) {
            if (empty($tenant->uuid)) {
                $tenant->uuid = Uuid::uuid4()->toString();
            }
        });
    }

    /**
     * Get the tenant's unique identifier (UUID).
     */
    public function getTenantUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Get the tenant's name or business name.
     */
    public function getTenantName(): string
    {
        return $this->name;
    }

    /**
     * Domini associati a questo tenant.
     */
    public function domains(): HasMany
    {
        return $this->hasMany(TenantDomain::class , 'tenant_uuid', 'uuid');
    }

    /**
     * Path associati a questo tenant.
     */
    public function paths(): HasMany
    {
        return $this->hasMany(TenantPath::class , 'tenant_uuid', 'uuid');
    }

    /**
     * Restituisce i dati del tenant per la sessione.
     */
    public function toSessionArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'metadata' => $this->metadata,
        ];
    }
}