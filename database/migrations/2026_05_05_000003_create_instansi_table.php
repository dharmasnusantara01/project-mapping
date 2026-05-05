<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instansi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi', 200);
            $table->text('alamat_instansi')->nullable();
            $table->string('telpon_instansi', 30)->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->foreignId('witel_id')->constrained('witel')->restrictOnDelete();
            $table->foreignId('account_manager_id')->constrained('account_managers')->restrictOnDelete();
            $table->foreignId('sector_id')->constrained()->restrictOnDelete();
            $table->string('public_summary', 500)->nullable();
            $table->boolean('is_public')->default(false);
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('is_public');
            $table->index('witel_id');
            $table->index('account_manager_id');
            $table->index('sector_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instansi');
    }
};
