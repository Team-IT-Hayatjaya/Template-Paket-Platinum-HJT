<?php

namespace App\Filament\Resources\IpBlocklists\Pages;

use App\Filament\Resources\IpBlocklists\IpBlocklistResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIpBlocklists extends ListRecords
{
    protected static string $resource = IpBlocklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Blokir IP Baru'),
        ];
    }
}
