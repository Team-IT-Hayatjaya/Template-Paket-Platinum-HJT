<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PermohonanInformasis\PermohonanInformasiResource;
use App\Models\PermohonanInformasi;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPermohonanWidget extends BaseWidget
{
    protected static ?string $heading = 'Permohonan Informasi Terbaru (Butuh Tindak Lanjut)';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PermohonanInformasi::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('nomor_registrasi')
                    ->label('No. Registrasi')
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('nama_pemohon')
                    ->label('Nama Pemohon'),

                TextColumn::make('rincian_informasi')
                    ->label('Informasi yang Diminta')
                    ->limit(40),

                TextColumn::make('status.nama')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Menunggu' => 'warning',
                        'Diproses' => 'info',
                        'Dikabulkan Sepenuhnya', 'Selesai' => 'success',
                        'Ditolak' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y H:i'),
            ])
            ->actions([
                Action::make('open')
                    ->label('Buka & Proses')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (PermohonanInformasi $record): string => PermohonanInformasiResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
