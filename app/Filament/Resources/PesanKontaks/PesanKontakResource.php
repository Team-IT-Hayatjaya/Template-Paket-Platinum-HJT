<?php

namespace App\Filament\Resources\PesanKontaks;

use App\Filament\Resources\PesanKontaks\Pages\EditPesanKontak;
use App\Filament\Resources\PesanKontaks\Pages\ListPesanKontaks;
use App\Models\PesanKontak;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PesanKontakResource extends Resource
{
    protected static ?string $model = PesanKontak::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static string | \UnitEnum | null $navigationGroup = 'Pelayanan Warga';

    protected static ?string $navigationLabel = 'Pesan & Aspirasi Warga';

    protected static ?string $modelLabel = 'Pesan Warga';

    protected static ?string $pluralModelLabel = 'Kotak Masuk Pesan & Aspirasi Warga';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $count = PesanKontak::where('is_read', false)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Uraian Pesan / Pengaduan')
                            ->schema([
                                TextInput::make('topik')
                                    ->label('Topik / Kategori')
                                    ->disabled(),

                                Textarea::make('pesan')
                                    ->label('Isi Pesan / Aspirasi')
                                    ->disabled()
                                    ->rows(6),
                            ]),

                        Section::make('Tindak Lanjut Admin')
                            ->schema([
                                Select::make('status')
                                    ->label('Status Pesan')
                                    ->options([
                                        'belum_dibaca' => 'Belum Dibaca',
                                        'sudah_dibaca' => 'Sudah Dibaca',
                                        'dibalas' => 'Sudah Dibalas',
                                    ])
                                    ->required(),

                                Textarea::make('catatan_admin')
                                    ->label('Catatan Internal / Respon')
                                    ->placeholder('Catat ringkasan tindak lanjut atau nomor kontak yang dihubungi...')
                                    ->rows(3),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Identitas Pengirim')
                            ->schema([
                                TextInput::make('nama')
                                    ->label('Nama Lengkap')
                                    ->disabled(),

                                TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->disabled(),

                                TextInput::make('telepon')
                                    ->label('Nomor Telepon / WhatsApp')
                                    ->disabled(),

                                TextInput::make('created_at')
                                    ->label('Waktu Masuk')
                                    ->disabled(),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Pengirim')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('email')
                    ->label('Email & Kontak')
                    ->description(fn (PesanKontak $record): string => $record->telepon ?? '-')
                    ->searchable(),

                TextColumn::make('topik')
                    ->label('Topik')
                    ->badge()
                    ->sortable(),

                TextColumn::make('pesan')
                    ->label('Isi Pesan')
                    ->limit(50),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'belum_dibaca' => 'warning',
                        'sudah_dibaca' => 'info',
                        'dibalas' => 'success',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'belum_dibaca' => 'Belum Dibaca',
                        'sudah_dibaca' => 'Sudah Dibaca',
                        'dibalas' => 'Sudah Dibalas',
                        default => ucfirst($state),
                    }),

                TextColumn::make('created_at')
                    ->label('Tgl Masuk')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'belum_dibaca' => 'Belum Dibaca',
                        'sudah_dibaca' => 'Sudah Dibaca',
                        'dibalas' => 'Sudah Dibalas',
                    ]),
            ])
            ->recordActions([
                EditAction::make()->label('Buka & Tindak Lanjut'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPesanKontaks::route('/'),
            'edit' => EditPesanKontak::route('/{record}/edit'),
        ];
    }
}
