<?php

namespace App\Filament\Resources\IpBlocklists\Pages;

use App\Filament\Resources\IpBlocklists\IpBlocklistResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIpBlocklist extends CreateRecord
{
    protected static string $resource = IpBlocklistResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['is_automatic'] = false;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
