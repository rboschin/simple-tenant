<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('tenant_uuid')->nullable()->after('id');
            // Nota: Il vincolo di integrità è gestito dal package via codice (BelongsToTenant trait)
            // per permettere maggiore flessibilità durante il seeding e testing.
            $table->index('tenant_uuid');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->dropForeign(['tenant_uuid']);
            $table->dropIndex(['tenant_uuid']);
            $table->dropColumn('tenant_uuid');
        });
    }
};