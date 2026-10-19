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
        Schema::table('halaman', function (Blueprint $table) {
            $table->string('berkas_pdf')->nullable()->after('gambar_utama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('halaman', function (Blueprint $table) {
            $table->dropColumn('berkas_pdf');
        });
    }
};
