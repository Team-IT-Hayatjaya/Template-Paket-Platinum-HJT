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
        Schema::create('dokumen_informasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_informasi')->cascadeOnDelete();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('ringkasan')->nullable();
            $table->integer('tahun')->index();
            $table->string('file_path');
            $table->string('tipe_file')->default('pdf');
            $table->string('ukuran_file')->nullable();
            $table->integer('dilihat')->default(0);
            $table->integer('diunduh')->default(0);
            $table->boolean('is_published')->default(true);
            $table->date('tanggal_terbit')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_informasi');
    }
};
