<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tipografi kustom instansi (paket Gold): identitas visual sepenuhnya
     * bersumber dari site_settings (logo, warna, dan kini tipografi).
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('heading_font', 100)->nullable()->after('secondary_color');
            $table->string('body_font', 100)->nullable()->after('heading_font');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['heading_font', 'body_font']);
        });
    }
};
