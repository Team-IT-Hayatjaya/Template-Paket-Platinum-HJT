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
        Schema::table('statistik', function (Blueprint $table) {
            $table->string('berkas_spreadsheet')->nullable()->after('berkas_pdf');
            $table->string('tipe_grafik', 30)->default('bar')->after('berkas_spreadsheet');
            $table->json('data_grafik')->nullable()->after('tipe_grafik');
            $table->json('data_tabel')->nullable()->after('data_grafik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statistik', function (Blueprint $table) {
            $table->dropColumn(['berkas_spreadsheet', 'tipe_grafik', 'data_grafik', 'data_tabel']);
        });
    }
};
