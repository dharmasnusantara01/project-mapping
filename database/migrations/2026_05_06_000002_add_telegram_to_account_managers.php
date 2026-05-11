<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_managers', function (Blueprint $table) {
            $table->string('telegram_chat_id', 50)->nullable()->after('phone');
            $table->timestamp('last_reminded_at')->nullable()->after('telegram_chat_id');
            $table->index('telegram_chat_id');
        });
    }

    public function down(): void
    {
        Schema::table('account_managers', function (Blueprint $table) {
            $table->dropIndex(['telegram_chat_id']);
            $table->dropColumn(['telegram_chat_id', 'last_reminded_at']);
        });
    }
};
