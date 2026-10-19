<?php

namespace App\Filament\Resources\IpBlocklists;

use App\Filament\Resources\IpBlocklists\Pages\CreateIpBlocklist;
use App\Filament\Resources\IpBlocklists\Pages\EditIpBlocklist;
use App\Filament\Resources\IpBlocklists\Pages\ListIpBlocklists;
use App\Models\IpBlocklist;
use App\Support\Package;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IpBlocklistResource extends Resource
{
    protected static ?string $model = IpBlocklist::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-no-symbol';

    protected static string | \UnitEnum | null $navigationGroup = 'Keamanan & Pengguna';

    protected static ?string $navigationLabel = 'Daftar Blokir IP';

    protected static ?string $modelLabel = 'Blokir IP';

    protected static ?string $pluralModelLabel = 'Daftar Blokir IP';

    protected static ?int $navigationSort = 4;

    /**
     * Fitur Keamanan Aktif hanya tersedia pada paket Gold ke atas.
     */
    public static function canAccess(): bool
    {
        return Package::has('security_hardening') && parent::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pemblokiran')
                    ->description('Permintaan dari alamat IP ini akan ditolak dengan kode 403 di seluruh portal, termasuk panel admin.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('ip_address')
                            ->label('Alamat IP')
                            ->placeholder('Contoh: 203.0.113.10')
                            ->required()
                            ->ip()
                            ->maxLength(45)
                            ->unique(ignoreRecord: true)
                            ->notIn(fn () => array_filter([request()->ip()]))
                            ->validationMessages([
                                'not_in' => 'Anda tidak dapat memblokir alamat IP yang sedang Anda gunakan.',
                            ]),

                        DateTimePicker::make('blocked_until')
                            ->label('Diblokir Hingga')
                            ->helperText('Kosongkan untuk blokir permanen.')
                            ->native(false)
                            ->seconds(false)
                            ->minDate(now()),

                        TextInput::make('reason')
                            ->label('Alasan Pemblokiran')
                            ->placeholder('Contoh: Percobaan brute force ke panel admin')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ip_address')
                    ->label('Alamat IP')
                    ->fontFamily('mono')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(60)
                    ->wrap()
                    ->placeholder('-'),

                IconColumn::make('is_automatic')
                    ->label('Otomatis')
                    ->boolean(),

                TextColumn::make('blocked_until')
                    ->label('Berlaku Hingga')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Permanen')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (IpBlocklist $record): string => $record->isActive() ? 'Aktif' : 'Kedaluwarsa')
                    ->color(fn (string $state): string => $state === 'Aktif' ? 'danger' : 'gray'),

                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->placeholder('Sistem'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('aktif')
                    ->label('Status Blokir')
                    ->trueLabel('Hanya yang aktif')
                    ->falseLabel('Hanya yang kedaluwarsa')
                    ->queries(
                        true: fn (Builder $q) => $q->active(),
                        false: fn (Builder $q) => $q->whereNotNull('blocked_until')->where('blocked_until', '<=', now()),
                    ),
                TernaryFilter::make('is_automatic')->label('Diblokir Otomatis'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->label('Buka Blokir'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Buka Blokir Terpilih'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIpBlocklists::route('/'),
            'create' => CreateIpBlocklist::route('/create'),
            'edit' => EditIpBlocklist::route('/{record}/edit'),
        ];
    }
}
