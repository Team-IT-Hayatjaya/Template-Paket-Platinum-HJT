<?php

namespace App\Filament\Resources\Datasets;

use App\Filament\Resources\Datasets\Pages\CreateDataset;
use App\Filament\Resources\Datasets\Pages\EditDataset;
use App\Filament\Resources\Datasets\Pages\ListDatasets;
use App\Models\Dataset;
use App\Support\Package;
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
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DatasetResource extends Resource
{
    protected static ?string $model = Dataset::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-circle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Publikasi & Informasi';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Portal Open Data';

    protected static ?string $modelLabel = 'Dataset Terbuka';

    protected static ?string $pluralModelLabel = 'Katalog Dataset Terbuka';

    public static function canAccess(): bool
    {
        return Package::has('open_data') && (auth()->user()?->hasAnyRole(['Super Admin', 'Admin Konten', 'Admin Unit']) ?? false);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Informasi Utama Dataset')
                            ->schema([
                                TextInput::make('judul')
                                    ->label('Judul Dataset')
                                    ->placeholder('Contoh: Jumlah Penduduk Berdasarkan Jenis Kelamin')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                    }),

                                TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->required()
                                    ->unique(Dataset::class, 'slug', ignoreRecord: true)
                                    ->maxLength(255),

                                Textarea::make('deskripsi')
                                    ->label('Uraian / Deskripsi Dataset')
                                    ->rows(3)
                                    ->placeholder('Penjelasan mengenai metodologi atau lingkup data...')
                                    ->maxLength(1000),

                                Grid::make(2)->schema([
                                    Select::make('kategori')
                                        ->label('Kategori Sektoral')
                                        ->options([
                                            'Kependudukan' => 'Kependudukan',
                                            'Kesehatan' => 'Kesehatan',
                                            'Pendidikan' => 'Pendidikan',
                                            'Ekonomi & Keuangan' => 'Ekonomi & Keuangan',
                                            'Sosial & Budaya' => 'Sosial & Budaya',
                                            'Lingkungan Hidup' => 'Lingkungan Hidup',
                                            'Infrastruktur' => 'Infrastruktur',
                                            'Pemerintahan' => 'Pemerintahan',
                                        ])
                                        ->searchable()
                                        ->required(),

                                    TextInput::make('sumber')
                                        ->label('Instansi Sumber Data')
                                        ->placeholder('Contoh: Dinas Kependudukan dan Pencatatan Sipil')
                                        ->required(),
                                ]),

                                Grid::make(2)->schema([
                                    TextInput::make('periode')
                                        ->label('Periode Data')
                                        ->placeholder('Contoh: 2026 atau Semester I 2026')
                                        ->required(),

                                    TextInput::make('penanggung_jawab')
                                        ->label('Penanggung Jawab Data')
                                        ->placeholder('Contoh: Bidang Pengelolaan Informasi')
                                        ->required(),
                                ]),
                            ]),

                        Section::make('Konfigurasi Visualisasi Grafik (Chart.js)')
                            ->schema([
                                Grid::make(3)->schema([
                                    Select::make('chart_type')
                                        ->label('Tipe Grafik Default')
                                        ->options([
                                            'bar' => 'Grafik Batang (Bar)',
                                            'line' => 'Grafik Garis (Line)',
                                            'pie' => 'Grafik Pai (Pie)',
                                        ])
                                        ->default('bar')
                                        ->required(),

                                    TextInput::make('chart_x_column')
                                        ->label('Nama Kolom Sumbu X (Label)')
                                        ->placeholder('Contoh: Kecamatan / Tahun')
                                        ->helperText('Nama kolom persis seperti di baris header CSV'),

                                    TextInput::make('chart_y_column')
                                        ->label('Nama Kolom Sumbu Y (Nilai)')
                                        ->placeholder('Contoh: Jumlah / Total')
                                        ->helperText('Nama kolom berisi angka untuk nilai grafik'),
                                ]),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Berkas & Publikasi')
                            ->schema([
                                FileUpload::make('berkas_csv')
                                    ->label('Berkas Data CSV')
                                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                                    ->disk('public')
                                    ->directory('datasets')
                                    ->required()
                                    ->maxSize(10240)
                                    ->helperText('Format CSV dengan baris pertama berupa header nama kolom.'),

                                DatePicker::make('tanggal_pembaruan')
                                    ->label('Tanggal Pembaruan')
                                    ->default(now())
                                    ->required(),

                                Select::make('unit_id')
                                    ->label('Unit / UPTD Pemilik')
                                    ->relationship('unit', 'nama')
                                    ->placeholder('Portal Utama (Induk)')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn () => Package::has('subdomains') && (bool) auth()->user()?->hasRole('Super Admin')),

                                Toggle::make('is_published')
                                    ->label('Terbitkan ke Publik')
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
                    ->label('Judul Dataset')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(45),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),

                TextColumn::make('periode')
                    ->label('Periode')
                    ->sortable(),

                TextColumn::make('row_count')
                    ->label('Baris Data')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('version')
                    ->label('Versi')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => "v{$state}.0"),

                TextColumn::make('total_unduhan')
                    ->label('Diunduh')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('tanggal_pembaruan', 'desc')
            ->filters([
                SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'Kependudukan' => 'Kependudukan',
                        'Kesehatan' => 'Kesehatan',
                        'Pendidikan' => 'Pendidikan',
                        'Ekonomi & Keuangan' => 'Ekonomi & Keuangan',
                        'Sosial & Budaya' => 'Sosial & Budaya',
                        'Lingkungan Hidup' => 'Lingkungan Hidup',
                        'Infrastruktur' => 'Infrastruktur',
                        'Pemerintahan' => 'Pemerintahan',
                    ]),
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
            'index' => ListDatasets::route('/'),
            'create' => CreateDataset::route('/create'),
            'edit' => EditDataset::route('/{record}/edit'),
        ];
    }
}
