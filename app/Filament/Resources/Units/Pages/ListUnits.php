<?php

namespace App\Filament\Resources\Units\Pages;

use App\Filament\Resources\Units\UnitResource;
use App\Models\Unit;
use App\Support\Package;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListUnits extends ListRecords
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        $limit = Package::limit('subdomains') ?? 5;
        $count = Unit::count();
        $isLimitReached = $count >= $limit;

        return [
            CreateAction::make()
                ->label('Tambah Unit / UPTD')
                ->disabled($isLimitReached)
                ->tooltip($isLimitReached ? "Batas kuota sub-domain paket ({$limit} unit) telah tercapai." : null)
                ->before(function (CreateAction $action) use ($isLimitReached, $limit) {
                    if ($isLimitReached) {
                        Notification::make()
                            ->title('Batas Kuota Tercapai')
                            ->body("Paket Platinum dibatasi maksimal {$limit} sub-domain UPTD/Bidang.")
                            ->danger()
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }
}
