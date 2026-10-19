<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fitur WhatsApp Bot Informasi Publik & Tiket Eskalasi Paket Platinum.
     */
    public function up(): void
    {
        Schema::create('whatsapp_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('kata_kunci', 100)->index();
            $table->string('pertanyaan');
            $table->text('jawaban');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('whatsapp_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket', 30)->unique()->index();
            $table->string('phone_number_masked', 30)->index();
            $table->text('pesan');
            $table->string('status', 30)->default('menunggu_petugas')->index(); // menunggu_petugas, sedang_diproses, selesai
            $table->text('catatan_petugas')->nullable();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ditanggapi_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('phone_hash', 64)->index();
            $table->string('phone_masked', 30)->nullable();
            $table->text('pesan_masuk')->nullable();
            $table->text('pesan_keluar')->nullable();
            $table->string('tipe_menu', 50)->default('menu_utama')->index();
            $table->string('status', 30)->default('sukses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversations');
        Schema::dropIfExists('whatsapp_tickets');
        Schema::dropIfExists('whatsapp_faqs');
    }
};
