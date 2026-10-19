<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel & kolom pendukung fitur Keamanan Aktif paket Gold:
     * penguncian akun, daftar blokir IP, dan riwayat pemeriksaan integritas berkas.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('failed_login_attempts')->default(0)->after('password');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
        });

        Schema::create('ip_blocklists', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->string('reason')->nullable();
            $table->timestamp('blocked_until')->nullable()->comment('NULL = blokir permanen');
            $table->boolean('is_automatic')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('blocked_until');
        });

        Schema::create('integrity_checks', function (Blueprint $table) {
            $table->id();
            $table->string('status', 20)->comment('baseline | clean | modified');
            $table->unsignedInteger('files_checked')->default(0);
            $table->json('modified_files')->nullable();
            $table->json('missing_files')->nullable();
            $table->json('new_files')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->index(['status', 'checked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integrity_checks');
        Schema::dropIfExists('ip_blocklists');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['failed_login_attempts', 'locked_until']);
        });
    }
};
