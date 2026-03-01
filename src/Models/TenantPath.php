<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class TenantPath extends Model
{
    protected $table = 'st_tenant_paths';

    protected $fillable = [
        'uuid',
        'tenant_uuid',
        'path',
    ];

    protected static function booted(): void
    {
        static::creating(function (self$path) {
            if (empty($path->uuid)) {
                $path->uuid = Uuid::uuid4()->toString();
            }
        });
    }

    /**
     * Tenant a cui appartiene questo path.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class , 'tenant_uuid', 'uuid');
    }
}