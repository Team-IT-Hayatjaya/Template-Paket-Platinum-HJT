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
        Schema::create('statistik', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->string('slug', 255)->unique();
            $table->string('kategori', 100)->index();
            $table->string('periode', 100);
            $table->integer('tahun')->default(2026)->index();
            $table->string('sumber', 255)->nullable();
            $table->longText('isi')->nullable(); // tabel rich text data statistik
            $table->string('gambar_infografis')->nullable();
            $table->string('berkas_pdf')->nullable();
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
        Schema::dropIfExists('statistik');
    }
};
