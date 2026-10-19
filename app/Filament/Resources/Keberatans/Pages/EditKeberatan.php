<?php

namespace App\Filament\Resources\Keberatans\Pages;

use App\Filament\Resources\Keberatans\KeberatanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKeberatan extends EditRecord
{
    protected static string $resource = KeberatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
