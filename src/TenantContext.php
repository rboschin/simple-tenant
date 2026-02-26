<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant;

use Rboschin\SimpleTenant\Models\Tenant;
use Illuminate\Support\Facades\Session;

class TenantContext
{
    protected ?Tenant $cachedTenant = null;

    /**
     * Imposta il tenant corrente in sessione.
     */
    public function set(Tenant $tenant): void
    {
        $this->cachedTenant = $tenant;

        Session::put($this->sessionKey(), $tenant->toSessionArray());
    }

    /**
     * Restituisce i dati del tenant dalla sessione.
     */
    public function get(): ?array
    {
        return Session::get($this->sessionKey());
    }

    /**
     * Restituisce il modello Tenant corrente.
     * Usa una cache in-memory per evitare query ripetute.
     */
    public function tenant(): ?Tenant
    {
        if ($this->cachedTenant) {
            return $this->cachedTenant;
        }

        $uuid = $this->uuid();

        if (!$uuid) {
            return null;
        }

        $this->cachedTenant = Tenant::where('uuid', $uuid)->first();

        return $this->cachedTenant;
    }

    /**
     * Restituisce l'UUID del tenant corrente.
     */
    public function uuid(): ?string
    {
        $data = $this->get();

        return $data['uuid'] ?? null;
    }

    /**
     * Restituisce il nome del tenant corrente.
     */
    public function name(): ?string
    {
        $data = $this->get();

        return $data['name'] ?? null;
    }

    /**
     * Verifica se un tenant è attivo nel contesto corrente.
     */
    public function check(): bool
    {
        return $this->uuid() !== null;
    }

    /**
     * Rimuove il tenant dalla sessione.
     */
    public function forget(): void
    {
        $this->cachedTenant = null;

        Session::forget($this->sessionKey());
    }

    /**
     * La chiave di sessione per i dati del tenant.
     */
    protected function sessionKey(): string
    {
        return config('simpletenant.session_key', 'simpletenant_tenant');
    }
}