<?php

namespace App\Filament\Resources\Keberatans\Pages;

use App\Filament\Resources\Keberatans\KeberatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKeberatans extends ListRecords
{
    protected static string $resource = KeberatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Keberatan'),
        ];
    }
}
