<?php

namespace App\Filament\Resources\IpBlocklists\Pages;

use App\Filament\Resources\IpBlocklists\IpBlocklistResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIpBlocklist extends EditRecord
{
    protected static string $resource = IpBlocklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Buka Blokir'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
