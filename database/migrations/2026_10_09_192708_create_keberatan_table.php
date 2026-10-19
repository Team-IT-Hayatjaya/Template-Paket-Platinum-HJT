<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('keberatan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_keberatan')->unique()->index();
            $table->foreignId('permohonan_id')->nullable()->constrained('permohonan_informasi')->nullOnDelete();
            $table->string('nomor_registrasi_permohonan');
            $table->string('nama_pemohon');
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('alasan_keberatan');
            $table->text('uraian_keberatan');
            $table->string('status')->default('diterima');
            $table->text('catatan_internal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keberatan');
    }
};
