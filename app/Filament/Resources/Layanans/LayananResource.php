<?php

namespace App\Filament\Resources\Layanans;

use App\Filament\Resources\Layanans\Pages\CreateLayanan;
use App\Filament\Resources\Layanans\Pages\EditLayanan;
use App\Filament\Resources\Layanans\Pages\ListLayanans;
use App\Models\Layanan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LayananResource extends Resource
{
    protected static ?string $model = Layanan::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static string | \UnitEnum | null $navigationGroup = 'Pelayanan Warga';

    protected static ?string $navigationLabel = 'Layanan Publik (PATEN)';

    protected static ?string $modelLabel = 'Layanan Publik';

    protected static ?string $pluralModelLabel = 'Daftar Layanan Publik (PATEN)';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Informasi Layanan')
                            ->schema([
                                TextInput::make('nama')
                                    ->label('Nama Layanan Publik')
                                    ->placeholder('Contoh: Rekomendasi Surat Keterangan Tidak Mampu (SKTM)')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->required()
                                    ->unique(Layanan::class, 'slug', ignoreRecord: true)
                                    ->maxLength(255),

                                Textarea::make('ringkasan')
                                    ->label('Ringkasan Pelayanan')
                                    ->rows(2)
                                    ->placeholder('Uraian singkat tujuan dan manfaat layanan...'),

                                RichEditor::make('persyaratan')
                                    ->label('Persyaratan Berkas & Dokumen')
                                    ->placeholder('Rincian dokumen yang wajib dibawa oleh pemohon...'),

                                RichEditor::make('alur')
                                    ->label('Alur / Prosedur Pengurusan')
                                    ->placeholder('Tahapan pengurusan dari loket pendaftaran hingga selesai...'),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Ketentuan & Regulasi')
                            ->schema([
                                TextInput::make('biaya')
                                    ->label('Biaya / Retribusi')
                                    ->default('Gratis (Rp 0)')
                                    ->required(),

                                TextInput::make('waktu_penyelesaian')
                                    ->label('Waktu Penyelesaian')
                                    ->default('1 Hari Kerja')
                                    ->required(),

                                TextInput::make('penanggung_jawab')
                                    ->label('Penanggung Jawab / Loket')
                                    ->default('Seksi Pelayanan Administrasi Terpadu (PATEN)')
                                    ->required(),

                                FileUpload::make('formulir_file')
                                    ->label('Formulir Unduhan (PDF)')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->disk('public')
                                    ->directory('formulir-layanan')
                                    ->maxSize(10240)
                                    ->helperText('Format PDF, maksimal 10 MB'),

                                TextInput::make('urutan')
                                    ->label('Nomor Urut Tampil')
                                    ->numeric()
                                    ->default(0),

                                Toggle::make('is_active')
                                    ->label('Status Aktif')
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
                TextColumn::make('nama')
                    ->label('Nama Layanan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(45),

                TextColumn::make('biaya')
                    ->label('Biaya')
                    ->badge()
                    ->color('success'),

                TextColumn::make('waktu_penyelesaian')
                    ->label('Durasi')
                    ->badge()
                    ->color('info'),

                TextColumn::make('penanggung_jawab')
                    ->label('Unit Pengampu')
                    ->limit(30),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('urutan')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->defaultSort('urutan')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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

    public static function getPages(): array
    {
        return [
            'index' => ListLayanans::route('/'),
            'create' => CreateLayanan::route('/create'),
            'edit' => EditLayanan::route('/{record}/edit'),
        ];
    }
}
