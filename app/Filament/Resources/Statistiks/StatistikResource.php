<?php

namespace App\Filament\Resources\Statistiks;

use App\Filament\Resources\Statistiks\Pages\CreateStatistik;
use App\Filament\Resources\Statistiks\Pages\EditStatistik;
use App\Filament\Resources\Statistiks\Pages\ListStatistiks;
use App\Models\Statistik;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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

class StatistikResource extends Resource
{
    protected static ?string $model = Statistik::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-pie';

    protected static string | \UnitEnum | null $navigationGroup = 'Publikasi & Informasi';

    protected static ?string $navigationLabel = 'Data & Statistik Wilayah';

    protected static ?string $modelLabel = 'Data Statistik';

    protected static ?string $pluralModelLabel = 'Katalog Data & Statistik Wilayah';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        $currentYear = (int) date('Y');

        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Informasi & Tabel Statistik')
                            ->schema([
                                TextInput::make('judul')
                                    ->label('Judul Data Statistik')
                                    ->placeholder('Contoh: Data Kepadatan Penduduk & Jumlah KK per Desa')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->required()
                                    ->unique(Statistik::class, 'slug', ignoreRecord: true)
                                    ->maxLength(255),

                                RichEditor::make('isi')
                                    ->label('Tabel Data & Uraian Statistik')
                                    ->placeholder('Masukkan tabel data statistik atau ringkasan angka capaian...')
                                    ->required(),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Klasifikasi & Berkas')
                            ->schema([
                                Select::make('kategori')
                                    ->label('Kategori Bidang')
                                    ->options([
                                        'Kependudukan' => 'Kependudukan',
                                        'Sosial & Ekonomi' => 'Sosial & Ekonomi',
                                        'Pendidikan' => 'Pendidikan',
                                        'Kesehatan' => 'Kesehatan',
                                        'Pertanian & Perikanan' => 'Pertanian & Perikanan',
                                        'Infrastruktur' => 'Infrastruktur',
                                    ])
                                    ->required(),

                                TextInput::make('periode')
                                    ->label('Periode Data')
                                    ->placeholder('Contoh: Semester 1 2026 atau Tahun 2025')
                                    ->required(),

                                TextInput::make('tahun')
                                    ->label('Tahun Data')
                                    ->numeric()
                                    ->default($currentYear)
                                    ->required(),

                                TextInput::make('sumber')
                                    ->label('Sumber Data')
                                    ->placeholder('Contoh: BPS Kabupaten / Seksi Pelayanan')
                                    ->nullable(),

                                FileUpload::make('berkas_spreadsheet')
                                    ->label('Unggah Data Excel / CSV (Otomatis Jadi Grafik)')
                                    ->helperText('Format .xlsx, .xls, .csv. Sistem otomatis mengonversi baris spreadsheet menjadi Grafik Interaktif & Tabel Data!')
                                    ->acceptedFileTypes([
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'application/vnd.ms-excel',
                                        'text/csv',
                                        'text/plain',
                                    ])
                                    ->disk('public')
                                    ->directory('statistik-data')
                                    ->maxSize(15360),

                                Select::make('tipe_grafik')
                                    ->label('Model Visualisasi Grafik')
                                    ->options([
                                        'bar' => '📊 Bar / Diagram Batang Vertikal',
                                        'line' => '📈 Line / Diagram Garis Tren',
                                        'pie' => '🥧 Pie / Diagram Lingkaran Proporsi',
                                        'doughnut' => '🍩 Doughnut / Diagram Donat',
                                        'polarArea' => '🧭 Polar Area / Radar Lingkar',
                                    ])
                                    ->default('bar')
                                    ->required(),

                                FileUpload::make('gambar_infografis')
                                    ->label('Gambar Infografis (Otomatis .webp)')
                                    ->image()
                                    ->disk('public')
                                    ->directory('statistik-infografis')
                                    ->maxSize(5120),

                                FileUpload::make('berkas_pdf')
                                    ->label('Unduhan Berkas Data (PDF Opsional)')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->disk('public')
                                    ->directory('statistik-dokumen')
                                    ->maxSize(10240),

                                TextInput::make('urutan')
                                    ->label('Nomor Urut')
                                    ->numeric()
                                    ->default(0),

                                Toggle::make('is_active')
                                    ->label('Tampilkan di Publik')
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
                    ->label('Judul Statistik')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(45),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),

                TextColumn::make('tipe_grafik')
                    ->label('Grafik')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn (?string $state) => strtoupper($state ?? 'BAR')),

                TextColumn::make('periode')
                    ->label('Periode')
                    ->badge()
                    ->color('info'),

                TextColumn::make('sumber')
                    ->label('Sumber')
                    ->limit(25),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Diperbarui')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('urutan')
            ->filters([
                SelectFilter::make('kategori')
                    ->options([
                        'Kependudukan' => 'Kependudukan',
                        'Sosial & Ekonomi' => 'Sosial & Ekonomi',
                        'Pendidikan' => 'Pendidikan',
                        'Kesehatan' => 'Kesehatan',
                        'Pertanian & Perikanan' => 'Pertanian & Perikanan',
                        'Infrastruktur' => 'Infrastruktur',
                    ]),
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
            'index' => ListStatistiks::route('/'),
            'create' => CreateStatistik::route('/create'),
            'edit' => EditStatistik::route('/{record}/edit'),
        ];
    }
}
