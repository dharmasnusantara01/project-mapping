<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('customer_name');
            $table->foreignId('sector_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('year');
            $table->string('public_summary')->nullable();
            $table->boolean('is_public')->default(false);
            $table->enum('public_status', ['berjalan', 'selesai'])->default('berjalan');
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_public', 'public_status']);
            $table->index(['sector_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
