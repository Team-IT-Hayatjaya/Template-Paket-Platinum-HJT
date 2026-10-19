<?php

namespace App\Filament\Resources\WhatsappTickets;

use App\Filament\Resources\WhatsappTickets\Pages\EditWhatsappTicket;
use App\Filament\Resources\WhatsappTickets\Pages\ListWhatsappTickets;
use App\Models\WhatsappTicket;
use App\Support\Package;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WhatsappTicketResource extends Resource
{
    protected static ?string $model = WhatsappTicket::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string | \UnitEnum | null $navigationGroup = 'Pelayanan Warga';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Tiket Aduan WhatsApp';

    protected static ?string $modelLabel = 'Tiket WhatsApp';

    protected static ?string $pluralModelLabel = 'Daftar Tiket Eskalasi WhatsApp';

    public static function canAccess(): bool
    {
        return Package::has('whatsapp_bot') && (auth()->user()?->hasAnyRole(['Super Admin', 'Admin Konten', 'Petugas PPID', 'Atasan PPID']) ?? false);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Uraian Pertanyaan Warga')
                            ->schema([
                                TextInput::make('nomor_tiket')
                                    ->label('Nomor Tiket')
                                    ->disabled(),

                                TextInput::make('phone_number_masked')
                                    ->label('Nomor WhatsApp Pengirim (Tersensor)')
                                    ->disabled(),

                                Textarea::make('pesan')
                                    ->label('Isi Pertanyaan / Permintaan Informasi')
                                    ->rows(4)
                                    ->disabled(),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Tindak Lanjut Petugas')
                            ->schema([
                                Select::make('status')
                                    ->label('Status Tiket')
                                    ->options([
                                        'menunggu_petugas' => 'Menunggu Petugas',
                                        'sedang_diproses' => 'Sedang Diproses',
                                        'selesai' => 'Selesai Ditindaklanjuti',
                                    ])
                                    ->required(),

                                Textarea::make('catatan_petugas')
                                    ->label('Catatan Tindak Lanjut Petugas')
                                    ->placeholder('Tuliskan respon atau informasi yang telah disampaikan...')
                                    ->rows(4),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_tiket')
                    ->label('No. Tiket')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone_number_masked')
                    ->label('Pengirim')
                    ->searchable(),

                TextColumn::make('pesan')
                    ->label('Isi Pertanyaan')
                    ->limit(65)
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'selesai' => 'success',
                        'sedang_diproses' => 'warning',
                        'menunggu_petugas' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'selesai' => 'Selesai',
                        'sedang_diproses' => 'Diproses',
                        'menunggu_petugas' => 'Menunggu',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Waktu Masuk')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'menunggu_petugas' => 'Menunggu Petugas',
                        'sedang_diproses' => 'Sedang Diproses',
                        'selesai' => 'Selesai',
                    ]),
            ])
            ->recordActions([
                EditAction::make()->label('Tanggapi'),
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
            'index' => ListWhatsappTickets::route('/'),
            'edit' => EditWhatsappTicket::route('/{record}/edit'),
        ];
    }
}
