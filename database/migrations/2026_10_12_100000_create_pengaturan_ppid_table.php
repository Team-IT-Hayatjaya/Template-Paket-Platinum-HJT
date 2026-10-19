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
        if (!Schema::hasTable('pengaturan_ppid')) {
            Schema::create('pengaturan_ppid', function (Blueprint $table) {
                $table->id();
                $table->string('kode_instansi')->default('LK');
                $table->string('nama_instansi')->default('Pemerintah Kecamatan Loa Kulu');
                $table->string('sub_nama_instansi')->nullable()->default('Kecamatan Loa Kulu');
                $table->string('nama_ppid')->default('PPID Utama');
                $table->string('warna_utama', 20)->default('#14304F');
                $table->string('warna_sekunder', 20)->default('#C9A227');
                $table->string('logo_path')->nullable();
                $table->string('favicon_path')->nullable();
                $table->text('alamat')->nullable();
                $table->string('telepon')->nullable();
                $table->string('email')->nullable();
                $table->string('email_atasan_ppid')->nullable();
                $table->text('jam_layanan')->nullable();
                $table->text('maklumat_pelayanan')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_ppid');
    }
};
