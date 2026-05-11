<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->timestamp('last_stale_alert_at')->nullable()->after('lost_reason');
            $table->index('last_stale_alert_at');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['last_stale_alert_at']);
            $table->dropColumn('last_stale_alert_at');
        });
    }
};
