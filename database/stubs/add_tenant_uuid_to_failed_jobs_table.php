<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->uuid('tenant_uuid')->nullable()->after('id');
            $table->index('tenant_uuid');
        });
    }

    public function down(): void
    {
        Schema::table('failed_jobs', function (Blueprint $table) {
            // $table->dropForeign(['tenant_uuid']);
            $table->dropIndex(['tenant_uuid']);
            $table->dropColumn('tenant_uuid');
        });
    }
};