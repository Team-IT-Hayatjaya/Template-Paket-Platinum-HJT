<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappConversation extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'phone_hash',
        'phone_masked',
        'pesan_masuk',
        'pesan_keluar',
        'tipe_menu',
        'status',
    ];

    /**
     * Sensor nomor telepon untuk perlindungan privasi publik (UU PDP).
     */
    public static function maskPhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9\+]/', '', $phone);
        $len = strlen($clean);

        if ($len <= 6) {
            return str_repeat('*', $len);
        }

        $prefix = substr($clean, 0, 5);
        $suffix = substr($clean, -4);
        return $prefix . '****' . $suffix;
    }
}
