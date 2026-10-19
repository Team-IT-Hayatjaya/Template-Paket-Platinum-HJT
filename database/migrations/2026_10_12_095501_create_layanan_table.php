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
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->string('slug', 255)->unique();
            $table->text('ringkasan')->nullable();
            $table->longText('persyaratan')->nullable();
            $table->longText('alur')->nullable();
            $table->string('biaya', 100)->default('Gratis (Rp 0)');
            $table->string('waktu_penyelesaian', 100)->default('1 Hari Kerja');
            $table->string('penanggung_jawab', 255)->default('Seksi Pelayanan Administrasi Terpadu (PATEN)');
            $table->string('formulir_file')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('urutan')->default(0)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};
