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
            $table->foreignId('instansi_id')->constrained('instansi')->cascadeOnDelete();
            $table->enum('stage', ['qualified', 'submit', 'win', 'lost'])->default('qualified');

            // Qualified
            $table->string('nama_project', 200);
            $table->string('nama_pelanggan', 200);
            $table->string('nomor_pic', 30);
            $table->string('jabatan_pic', 100);
            $table->enum('division', ['government', 'enterprise', 'sme']);
            $table->date('estimasi_go_live');
            $table->decimal('revenue', 15, 2);

            // Submit
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('durasi_bulan')->nullable();

            // Win
            $table->date('tanggal_win')->nullable();
            $table->date('tanggal_go_live')->nullable();
            $table->date('kontrak_sampai')->nullable();
            $table->string('skema_penagihan', 100)->nullable();
            $table->string('file_pks')->nullable();
            $table->string('file_po')->nullable();
            $table->string('file_npwp')->nullable();

            // Lost
            $table->string('lost_reason', 500)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('instansi_id');
            $table->index('stage');
            $table->index(['stage', 'instansi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
