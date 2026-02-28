<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Rboschin\SimpleTenant\Models\Tenant;
use Rboschin\SimpleTenant\Models\TenantPath;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create the Tenant
        $tenant = Tenant::create([
            'name' => 'ACME Corporation',
            'slug' => 'acme-corp',
            'email' => 'admin@acme.example.com',
            'is_active' => true,
            'metadata' => [
                'plan' => 'premium',
                'custom_theme' => 'dark',
            ],
        ]);

        // 2. Create the Tenant Path (for path-based identification)
        TenantPath::create([
            'tenant_uuid' => $tenant->uuid,
            'path' => 'acme', // URL will be: domain.com/acme/...
        ]);

        // 3. Create the Tenant User
        // Note: This assumes the User model uses the BelongsToTenant trait
        // and has the tenant_uuid column.
        User::create([
            'name' => 'Mario Rossi',
            'email' => 'mario@acme.example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'tenant_uuid' => $tenant->uuid,
            'remember_token' => Str::random(10),
        ]);

        $this->command->info('Tenant "ACME Corporation" created with path "/acme" and user "mario@acme.example.com".');
    }
}