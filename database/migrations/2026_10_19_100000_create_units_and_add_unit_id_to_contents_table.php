<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fitur Multi Sub-domain Paket Platinum: Tabel units dan penambahan unit_id pada konten.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('subdomain', 60)->unique()->index();
            $table->string('singkatan', 30)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('alamat')->nullable();
            $table->string('email')->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('logo')->nullable();
            $table->string('warna_tema', 20)->nullable()->default('#1e40af');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        // Tambah unit_id pada users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('id')->constrained('units')->nullOnDelete();
        });

        // Tambah unit_id pada seluruh modul konten publik
        $contentTables = [
            'berita',
            'galeri',
            'layanan',
            'dokumen_informasi',
            'halaman',
            'page_views',
            'banners',
            'navigation_menus',
        ];

        foreach ($contentTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('unit_id')->nullable()->index()->constrained('units')->cascadeOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $contentTables = [
            'navigation_menus',
            'banners',
            'page_views',
            'halaman',
            'dokumen_informasi',
            'layanan',
            'galeri',
            'berita',
        ];

        foreach ($contentTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'unit_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('unit_id');
                });
            }
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'unit_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('unit_id');
            });
        }

        Schema::dropIfExists('units');
    }
};
