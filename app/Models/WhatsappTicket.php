<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class WhatsappTicket extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'whatsapp_tickets';

    protected $fillable = [
        'nomor_tiket',
        'phone_number_masked',
        'pesan',
        'status',
        'catatan_petugas',
        'petugas_id',
        'ditanggapi_at',
    ];

    protected $casts = [
        'ditanggapi_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('whatsapp_tickets')
            ->setDescriptionForEvent(fn (string $eventName) => "Tiket WA {$this->nomor_tiket} {$eventName}");
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Generate nomor tiket otomatis unik.
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'WA-' . date('Ym') . '-';
        $latest = static::where('nomor_tiket', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        $seq = 1;
        if ($latest && preg_match('/-(\d{4})$/', $latest->nomor_tiket, $matches)) {
            $seq = (int) $matches[1] + 1;
        }

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
