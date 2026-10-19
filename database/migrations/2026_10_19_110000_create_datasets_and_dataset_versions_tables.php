<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fitur Modul Open Data Paket Platinum.
     */
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('kategori', 100)->index();
            $table->string('sumber', 150);
            $table->string('periode', 100);
            $table->string('penanggung_jawab', 150);
            $table->string('berkas_csv');
            $table->date('tanggal_pembaruan');
            $table->string('chart_type', 20)->default('bar'); // bar, line, pie
            $table->string('chart_x_column')->nullable();
            $table->string('chart_y_column')->nullable();
            $table->unsignedInteger('row_count')->default(0);
            $table->json('schema_columns')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->unsignedInteger('total_unduhan')->default(0);
            $table->unsignedInteger('total_views')->default(0);
            $table->boolean('is_published')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('dataset_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained('datasets')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('berkas_csv');
            $table->string('catatan_perubahan')->nullable();
            $table->unsignedInteger('row_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['dataset_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dataset_versions');
        Schema::dropIfExists('datasets');
    }
};
