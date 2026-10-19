<?php

namespace App\Filament\Resources\Keberatans;

use App\Filament\Resources\Keberatans\Pages\CreateKeberatan;
use App\Filament\Resources\Keberatans\Pages\EditKeberatan;
use App\Filament\Resources\Keberatans\Pages\ListKeberatans;
use App\Models\Keberatan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KeberatanResource extends Resource
{
    protected static ?string $model = Keberatan::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-scale';

    protected static string | \UnitEnum | null $navigationGroup = 'Layanan PPID';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Keberatan Informasi';

    protected static ?string $modelLabel = 'Keberatan Informasi';

    protected static ?string $pluralModelLabel = 'Keberatan Informasi Publik';

    public static function getNavigationBadge(): ?string
    {
        $count = Keberatan::whereIn('status', ['diterima', 'diproses'])->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Uraian & Alasan Keberatan')
                            ->description('Pernyataan ketidakpuasan pemohon terhadap pelayanan informasi publik')
                            ->schema([
                                TextInput::make('nomor_keberatan')
                                    ->label('Nomor Registrasi Keberatan')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),

                                TextInput::make('nomor_registrasi_permohonan')
                                    ->label('Nomor Registrasi Permohonan Terkait')
                                    ->required(),

                                TextInput::make('alasan_keberatan')
                                    ->label('Alasan Pengajuan Keberatan')
                                    ->required()
                                    ->maxLength(255),

                                Textarea::make('uraian_keberatan')
                                    ->label('Rincian & Kronologi Keberatan')
                                    ->required()
                                    ->rows(4),
                            ]),

                        Section::make('Tanggapan Atasan PPID / Catatan Internal')
                            ->description('Keputusan resmi dan catatan evaluasi penanganan keberatan')
                            ->schema([
                                Textarea::make('catatan_internal')
                                    ->label('Catatan Penanganan & Tindak Lanjut')
                                    ->placeholder('Tuliskan dasar pertimbangan dan arahan penyelesaian keberatan...')
                                    ->rows(4),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Status Penanganan')
                            ->schema([
                                Select::make('status')
                                    ->label('Status Keberatan')
                                    ->options([
                                        'diterima' => 'Diterima (Menunggu Penelaahan)',
                                        'diproses' => 'Sedang Diproses oleh Atasan PPID',
                                        'dikabulkan_sebagian' => 'Dikabulkan Sebagian',
                                        'dikabulkan_penuh' => 'Dikabulkan Sepenuhnya',
                                        'ditolak' => 'Ditolak / Tidak Dapat Diterima',
                                        'selesai' => 'Selesai Ditanggapi',
                                    ])
                                    ->required()
                                    ->default('diterima'),
                            ]),

                        Section::make('Data Pemohon')
                            ->schema([
                                TextInput::make('nama_pemohon')
                                    ->label('Nama Pemohon')
                                    ->required(),

                                TextInput::make('telepon')
                                    ->label('Nomor Telepon / WhatsApp')
                                    ->tel(),

                                TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->email(),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_keberatan')
                    ->label('No. Keberatan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('nomor_registrasi_permohonan')
                    ->label('No. Permohonan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_pemohon')
                    ->label('Nama Pemohon')
                    ->searchable(),

                TextColumn::make('alasan_keberatan')
                    ->label('Alasan Keberatan')
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->alasan_keberatan),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'diterima' => 'Diterima',
                        'diproses' => 'Diproses',
                        'dikabulkan_sebagian' => 'Dikabulkan Sebagian',
                        'dikabulkan_penuh' => 'Dikabulkan Penuh',
                        'ditolak' => 'Ditolak',
                        'selesai' => 'Selesai',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'diterima' => 'warning',
                        'diproses' => 'info',
                        'dikabulkan_penuh', 'selesai' => 'success',
                        'dikabulkan_sebagian' => 'primary',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'diterima' => 'Diterima',
                        'diproses' => 'Diproses',
                        'dikabulkan_sebagian' => 'Dikabulkan Sebagian',
                        'dikabulkan_penuh' => 'Dikabulkan Penuh',
                        'ditolak' => 'Ditolak',
                        'selesai' => 'Selesai',
                    ]),
            ])
            ->actions([
                EditAction::make()->label('Tinjau & Tanggapi'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKeberatans::route('/'),
            'create' => CreateKeberatan::route('/create'),
            'edit' => EditKeberatan::route('/{record}/edit'),
        ];
    }
}
