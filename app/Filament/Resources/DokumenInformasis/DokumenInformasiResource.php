<?php

namespace App\Filament\Resources\DokumenInformasis;

use App\Filament\Resources\DokumenInformasis\Pages\CreateDokumenInformasi;
use App\Filament\Resources\DokumenInformasis\Pages\EditDokumenInformasi;
use App\Filament\Resources\DokumenInformasis\Pages\ListDokumenInformasis;
use App\Models\DokumenInformasi;
use App\Models\KategoriInformasi;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DokumenInformasiResource extends Resource
{
    protected static ?string $model = DokumenInformasi::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-archive-box';

    protected static string | \UnitEnum | null $navigationGroup = 'Layanan PPID';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Daftar Informasi Publik (DIP)';

    protected static ?string $modelLabel = 'Dokumen Informasi';

    protected static ?string $pluralModelLabel = 'Dokumen Informasi Publik';

    public static function form(Schema $schema): Schema
    {
        $currentYear = (int) date('Y');
        $yearOptions = [];
        for ($y = $currentYear + 1; $y >= $currentYear - 10; $y--) {
            $yearOptions[$y] = (string) $y;
        }

        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Informasi Dokumen')
                            ->schema([
                                TextInput::make('judul')
                                    ->label('Judul Dokumen')
                                    ->placeholder('Contoh: Laporan Akuntabilitas Kinerja Instansi Pemerintah (LAKIP)')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                    }),

                                TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->required()
                                    ->unique(DokumenInformasi::class, 'slug', ignoreRecord: true)
                                    ->maxLength(255),

                                Textarea::make('ringkasan')
                                    ->label('Ringkasan / Deskripsi Dokumen')
                                    ->rows(3)
                                    ->maxLength(500),

                                FileUpload::make('file_path')
                                    ->label('Berkas Dokumen (Wajib Format PDF)')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(10240) // 10 MB = 10,240 KB
                                    ->disk('public')
                                    ->directory('dokumen')
                                    ->required()
                                    ->helperText('Maksimal ukuran berkas 10 MB, hanya ekstensi .pdf yang diperbolehkan'),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Klasifikasi & Publikasi')
                            ->schema([
                                Select::make('kategori_id')
                                    ->label('Kategori Informasi PPID')
                                    ->options(fn () => KategoriInformasi::pluck('nama', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('tahun')
                                    ->label('Tahun Dokumen')
                                    ->options($yearOptions)
                                    ->default($currentYear)
                                    ->required(),

                                DatePicker::make('tanggal_terbit')
                                    ->label('Tanggal Penetapan / Terbit')
                                    ->default(now()),

                                Toggle::make('is_published')
                                    ->label('Publikasikan Dokumen')
                                    ->default(true),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul')
                    ->label('Judul Dokumen')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                TextColumn::make('kategori.nama')
                    ->label('Kategori PPID')
                    ->badge()
                    ->sortable(),

                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('ukuran_file')
                    ->label('Ukuran')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('diunduh')
                    ->label('Diunduh')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama'),
                SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        $years = DokumenInformasi::distinct()->orderByDesc('tahun')->pluck('tahun', 'tahun')->toArray();
                        return $years ?: [(int) date('Y') => date('Y')];
                    }),
                TernaryFilter::make('is_published')
                    ->label('Status Terbit'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDokumenInformasis::route('/'),
            'create' => CreateDokumenInformasi::route('/create'),
            'edit' => EditDokumenInformasi::route('/{record}/edit'),
        ];
    }
}
