<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('city');
            $table->string('province');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('is_manual_override')->default(false);
            $table->timestamp('geocoded_at')->nullable();
            $table->boolean('is_primary')->default(true);
            $table->timestamps();

            $table->index('project_id');
            $table->index('province');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_locations');
    }
};
