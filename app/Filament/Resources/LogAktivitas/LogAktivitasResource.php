<?php

namespace App\Filament\Resources\LogAktivitas;

use App\Filament\Resources\LogAktivitas\Pages\ListLogAktivitas;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class LogAktivitasResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clock';

    protected static string | \UnitEnum | null $navigationGroup = 'Keamanan & Pengguna';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Audit Trail & Log Aktivitas';

    protected static ?string $modelLabel = 'Log Aktivitas';

    protected static ?string $pluralModelLabel = 'Catatan Audit Trail & Log Aktivitas';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('log_name')
                    ->label('Kategori Log')
                    ->disabled(),

                TextInput::make('description')
                    ->label('Deskripsi Tindakan')
                    ->disabled(),

                TextInput::make('event')
                    ->label('Peristiwa (Event)')
                    ->disabled(),

                Textarea::make('properties')
                    ->label('Rincian Parameter & Perubahan')
                    ->disabled()
                    ->rows(6)
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu Kejadian')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('log_name')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'auth' => 'info',
                        'security' => 'danger',
                        'content' => 'success',
                        default => 'primary',
                    }),

                TextColumn::make('description')
                    ->label('Aktivitas')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('causer.name')
                    ->label('Pelaku (Aparatur)')
                    ->default('Sistem / Publik')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('event')
                    ->label('Jenis Event')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created', 'login' => 'success',
                        'updated' => 'warning',
                        'deleted', 'failed' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Kategori')
                    ->options([
                        'auth' => 'Autentikasi (Login/Logout)',
                        'security' => 'Keamanan & Proteksi',
                        'default' => 'Sistem & Konten',
                    ]),
            ])
            ->actions([
                ViewAction::make()->label('Detail'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLogAktivitas::route('/'),
        ];
    }
}
