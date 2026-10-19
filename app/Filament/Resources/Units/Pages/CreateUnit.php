<?php

namespace App\Filament\Resources\Units\Pages;

use App\Filament\Resources\Units\UnitResource;
use App\Models\Unit;
use App\Support\Package;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUnit extends CreateRecord
{
    protected static string $resource = UnitResource::class;

    protected function beforeCreate(): void
    {
        $limit = Package::limit('subdomains') ?? 5;
        if (Unit::count() >= $limit) {
            Notification::make()
                ->title('Batas Kuota Tercapai')
                ->body("Jumlah sub-domain telah mencapai batas maksimal ({$limit} unit).")
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
