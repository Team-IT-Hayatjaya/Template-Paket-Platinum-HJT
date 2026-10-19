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
        if (Schema::hasTable('berita') && !Schema::hasColumn('berita', 'alt_text')) {
            Schema::table('berita', function (Blueprint $table) {
                $table->string('alt_text')->nullable()->after('gambar_utama');
            });
        }

        if (Schema::hasTable('galeri') && !Schema::hasColumn('galeri', 'alt_text')) {
            Schema::table('galeri', function (Blueprint $table) {
                $table->string('alt_text')->nullable()->after('gambar');
            });
        }

        if (Schema::hasTable('banners') && !Schema::hasColumn('banners', 'alt_text')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->string('alt_text')->nullable()->after('gambar');
            });
        }

        if (Schema::hasTable('halaman') && !Schema::hasColumn('halaman', 'alt_text')) {
            Schema::table('halaman', function (Blueprint $table) {
                $table->string('alt_text')->nullable()->after('gambar_utama');
            });
        }

        if (Schema::hasTable('statistik') && !Schema::hasColumn('statistik', 'alt_text')) {
            Schema::table('statistik', function (Blueprint $table) {
                $table->string('alt_text')->nullable()->after('gambar_infografis');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('berita') && Schema::hasColumn('berita', 'alt_text')) {
            Schema::table('berita', function (Blueprint $table) {
                $table->dropColumn('alt_text');
            });
        }

        if (Schema::hasTable('galeri') && Schema::hasColumn('galeri', 'alt_text')) {
            Schema::table('galeri', function (Blueprint $table) {
                $table->dropColumn('alt_text');
            });
        }

        if (Schema::hasTable('banners') && Schema::hasColumn('banners', 'alt_text')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('alt_text');
            });
        }

        if (Schema::hasTable('halaman') && Schema::hasColumn('halaman', 'alt_text')) {
            Schema::table('halaman', function (Blueprint $table) {
                $table->dropColumn('alt_text');
            });
        }

        if (Schema::hasTable('statistik') && Schema::hasColumn('statistik', 'alt_text')) {
            Schema::table('statistik', function (Blueprint $table) {
                $table->dropColumn('alt_text');
            });
        }
    }
};
