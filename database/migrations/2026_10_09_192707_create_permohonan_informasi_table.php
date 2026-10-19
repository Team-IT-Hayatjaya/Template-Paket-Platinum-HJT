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
        Schema::create('permohonan_informasi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_registrasi')->unique()->index();
            $table->foreignId('status_id')->index()->constrained('status_permohonan');
            $table->string('kategori_pemohon')->default('perorangan'); // perorangan / badan_hukum / kelompok
            $table->string('nama_pemohon');
            $table->text('nik')->nullable(); // Encrypted cast di Eloquent
            $table->text('alamat');
            $table->string('telepon');
            $table->string('email');
            $table->string('pekerjaan')->nullable();
            $table->string('identitas_file_path')->nullable(); // Disimpan di storage private
            $table->string('surat_kuasa_file_path')->nullable();
            $table->string('akta_badan_hukum_file_path')->nullable();
            $table->text('rincian_informasi');
            $table->text('tujuan_penggunaan');
            $table->string('cara_memperoleh_informasi')->default('salinan'); // melihat / salinan
            $table->string('cara_mendapat_salinan')->default('email'); // langsung / pos / email / soft_copy
            $table->dateTime('tenggat_jawab')->nullable(); // 10 hari kerja UU KIP
            $table->dateTime('tenggat_diperpanjang')->nullable();
            $table->text('catatan_internal')->nullable(); // Status manual internal admin
            $table->string('jawaban_file_path')->nullable();
            $table->text('jawaban_teks')->nullable();
            $table->dateTime('tanggal_dijawab')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_informasi');
    }
};
