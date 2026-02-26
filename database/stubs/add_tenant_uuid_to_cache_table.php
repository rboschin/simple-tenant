<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cache', function (Blueprint $table) {
            $table->uuid('tenant_uuid')->nullable()->after('key');
            $table->index('tenant_uuid');
        });
    }

    public function down(): void
    {
        Schema::table('cache', function (Blueprint $table) {
            $table->dropIndex(['tenant_uuid']);
            $table->dropColumn('tenant_uuid');
        });
    }
};