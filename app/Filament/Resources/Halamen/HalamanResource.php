<?php

namespace App\Filament\Resources\Halamen;

use App\Filament\Resources\Halamen\Pages\CreateHalaman;
use App\Filament\Resources\Halamen\Pages\EditHalaman;
use App\Filament\Resources\Halamen\Pages\ListHalamen;
use App\Models\Halaman;
use App\Services\ImageConversionService;
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

class HalamanResource extends Resource
{
    protected static ?string $model = Halaman::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static string | \UnitEnum | null $navigationGroup = 'Publikasi & Informasi';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Halaman Profil & Statis';

    protected static ?string $modelLabel = 'Halaman';

    protected static ?string $pluralModelLabel = 'Halaman Profil & Statis';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Konten Halaman')
                            ->description('Profil, visi misi, struktur organisasi, tugas dan fungsi, dll.')
                            ->schema([
                                TextInput::make('judul')
                                    ->label('Judul Halaman')
                                    ->placeholder('Contoh: Visi dan Misi')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                    }),

                                TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->required()
                                    ->unique(Halaman::class, 'slug', ignoreRecord: true)
                                    ->maxLength(255),

                                Textarea::make('ringkasan')
                                    ->label('Ringkasan / Pengantar Singkat')
                                    ->rows(2)
                                    ->maxLength(500),

                                RichEditor::make('isi')
                                    ->label('Isi Lengkap Halaman')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Optimasi Mesin Pencari (SEO)')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->placeholder('Judul SEO')
                                    ->maxLength(255),

                                Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->rows(2)
                                    ->placeholder('Deskripsi SEO')
                                    ->maxLength(255),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Pengaturan & Tampilan')
                            ->schema([
                                TextInput::make('urutan')
                                    ->label('Urutan Menu')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Angka lebih kecil tampil lebih awal'),

                                Toggle::make('is_active')
                                    ->label('Aktif / Publik')
                                    ->default(true)
                                    ->helperText('Jika nonaktif, halaman tidak dapat diakses publik'),

                                Toggle::make('show_in_menu')
                                    ->label('Tampilkan di Navigasi Profil')
                                    ->default(true)
                                    ->helperText('Tampilkan di dropdown menu profil'),
                            ]),

                        Section::make('Gambar Utama / Banner Halaman')
                            ->description('Dikonversi otomatis ke format WebP')
                            ->schema([
                                FileUpload::make('gambar_utama')
                                    ->label('Berkas Gambar')
                                    ->image()
                                    ->disk('public')
                                    ->directory('halaman')
                                    ->maxSize(5120)
                                    ->saveUploadedFileUsing(function ($file) {
                                        return app(ImageConversionService::class)->storeAsWebp($file, 'halaman');
                                    }),
                            ]),
                        Section::make('Lampiran Dokumen PDF Resmi')
                            ->description('Tampilkan berkas PDF langsung di dalam halaman')
                            ->schema([
                                FileUpload::make('berkas_pdf')
                                    ->label('Berkas PDF Resmi')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->disk('public')
                                    ->directory('dokumen-halaman')
                                    ->maxSize(15360)
                                    ->helperText('Jika diisi, isi dokumen PDF akan langsung dimunculkan di halaman publik.'),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('urutan')
                    ->label('Urutan')
                    ->sortable()
                    ->badge(),

                TextColumn::make('judul')
                    ->label('Judul Halaman')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->color('gray')
                    ->copyable(),

                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('show_in_menu')
                    ->label('Di Menu')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('urutan', 'asc')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
                TernaryFilter::make('show_in_menu')
                    ->label('Tampil di Menu'),
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
            'index' => ListHalamen::route('/'),
            'create' => CreateHalaman::route('/create'),
            'edit' => EditHalaman::route('/{record}/edit'),
        ];
    }
}
