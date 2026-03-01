<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('st_tenant_domains', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('tenant_uuid');
            $table->string('domain')->unique();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('tenant_uuid')
                ->references('uuid')
                ->on('tenants')
                ->cascadeOnDelete();

            $table->index('domain');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('st_tenant_domains');
    }
};