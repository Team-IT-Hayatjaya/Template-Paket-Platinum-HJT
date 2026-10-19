<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class SiteSetting extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'site_settings';

    protected $fillable = [
        'institution_name',
        'institution_acronym',
        'logo_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'heading_font',
        'body_font',
        'address',
        'phone',
        'email',
        'social_media',
        'service_hours',
    ];

    protected $casts = [
        'social_media' => 'array',
    ];

    /**
     * Get or initialize the single instance of site settings.
     */
    public static function current(): self
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('site_settings')) {
                return new static([
                    'institution_name' => 'Pemerintah Kecamatan Loa Kulu',
                    'institution_acronym' => 'Kec. Loa Kulu',
                    'primary_color' => '#14304F',
                    'secondary_color' => '#C9A227',
                    'address' => 'Jl. Yos Sudarso No. 01, Loa Kulu, Kabupaten Kutai Kartanegara, Kalimantan Timur 75574',
                    'phone' => '(0541) 661001',
                    'email' => 'kecamatan.loakulu@kukarkab.go.id',
                    'service_hours' => "Senin - Kamis: 08.00 - 16.00 WITA\nJumat: 08.00 - 15.30 WITA",
                ]);
            }

            return static::firstOrCreate([], [
                'institution_name' => 'Pemerintah Kecamatan Loa Kulu',
                'institution_acronym' => 'Kec. Loa Kulu',
                'primary_color' => '#14304F',
                'secondary_color' => '#C9A227',
                'address' => 'Jl. Yos Sudarso No. 01, Loa Kulu, Kabupaten Kutai Kartanegara, Kalimantan Timur 75574',
                'phone' => '(0541) 661001',
                'email' => 'kecamatan.loakulu@kukarkab.go.id',
                'service_hours' => "Senin - Kamis: 08.00 - 16.00 WITA\nJumat: 08.00 - 15.30 WITA",
                'social_media' => [
                    'facebook' => 'https://facebook.com/kecamatanloakulu',
                    'instagram' => 'https://instagram.com/kecamatanloakulu',
                    'twitter' => '',
                    'youtube' => 'https://youtube.com/@kecamatanloakulu',
                ],
            ]);
        } catch (\Throwable $e) {
            return new static([
                'institution_name' => 'Pemerintah Kecamatan Loa Kulu',
                'institution_acronym' => 'Kec. Loa Kulu',
                'primary_color' => '#14304F',
                'secondary_color' => '#C9A227',
                'address' => 'Jl. Yos Sudarso No. 01, Loa Kulu, Kabupaten Kutai Kartanegara, Kalimantan Timur 75574',
                'phone' => '(0541) 661001',
                'email' => 'kecamatan.loakulu@kukarkab.go.id',
                'service_hours' => "Senin - Kamis: 08.00 - 16.00 WITA\nJumat: 08.00 - 15.30 WITA",
            ]);
        }
    }

    /**
     * Alias for current() to ensure backwards and template compatibility.
     */
    public static function getSettings(): self
    {
        return static::current();
    }

    /**
     * Spatie activitylog configuration.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'institution_name',
                'institution_acronym',
                'primary_color',
                'secondary_color',
                'address',
                'phone',
                'email',
                'social_media',
                'service_hours',
            ])
            ->logOnlyDirty()
            ->useLogName('site_settings')
            ->setDescriptionForEvent(fn (string $eventName) => "Pengaturan instansi {$eventName}");
    }
}
