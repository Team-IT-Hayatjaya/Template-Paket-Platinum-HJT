<?php

namespace App\Filament\Resources\Beritas;

use App\Filament\Resources\Beritas\Pages\CreateBerita;
use App\Filament\Resources\Beritas\Pages\EditBerita;
use App\Filament\Resources\Beritas\Pages\ListBeritas;
use App\Models\Berita;
use App\Services\ImageConversionService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BeritaResource extends Resource
{
    protected static ?string $model = Berita::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-newspaper';

    protected static string | \UnitEnum | null $navigationGroup = 'Publikasi & Informasi';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Berita & Artikel';

    protected static ?string $modelLabel = 'Berita';

    protected static ?string $pluralModelLabel = 'Berita & Artikel';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Konten Berita')
                            ->schema([
                                TextInput::make('judul')
                                    ->label('Judul Berita')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                    }),

                                TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->required()
                                    ->unique(Berita::class, 'slug', ignoreRecord: true)
                                    ->maxLength(255),

                                Textarea::make('ringkasan')
                                    ->label('Ringkasan / Abstrak')
                                    ->rows(3)
                                    ->placeholder('Ringkasan singkat yang menarik untuk kartu pengantar berita...')
                                    ->maxLength(500),

                                RichEditor::make('isi')
                                    ->label('Isi Lengkap Berita')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Optimasi Mesin Pencari (SEO)')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->placeholder('Judul halaman untuk mesin pencari')
                                    ->maxLength(255),

                                Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->rows(2)
                                    ->placeholder('Deskripsi singkat untuk snippet Google & Open Graph')
                                    ->maxLength(255),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Publikasi & Kategori')
                            ->schema([
                                Select::make('status')
                                    ->label('Status Berita')
                                    ->options([
                                        'draft' => 'Draft (Konsep)',
                                        'terbit' => 'Terbit (Publik)',
                                        'terjadwal' => 'Terjadwal',
                                    ])
                                    ->default('terbit')
                                    ->required()
                                    ->live(),

                                DateTimePicker::make('tanggal_terbit')
                                    ->label('Waktu Terbit')
                                    ->default(now())
                                    ->required(),

                                Select::make('kategori')
                                    ->label('Kategori')
                                    ->options([
                                        'Pemerintahan' => 'Pemerintahan',
                                        'Pembangunan' => 'Pembangunan',
                                        'Kemasyarakatan' => 'Kemasyarakatan',
                                        'Pengumuman' => 'Pengumuman',
                                        'Kesehatan' => 'Kesehatan',
                                        'Pendidikan' => 'Pendidikan',
                                    ])
                                    ->default('Pemerintahan')
                                    ->required(),

                                Select::make('unit_id')
                                    ->label('Unit / UPTD')
                                    ->relationship('unit', 'nama')
                                    ->placeholder('Portal Utama (Induk)')
                                    ->visible(fn () => \App\Support\Package::has('subdomains') && (bool) auth()->user()?->hasRole('Super Admin'))
                                    ->searchable()
                                    ->preload(),

                                TagsInput::make('tags')
                                    ->label('Tag / Kata Kunci')
                                    ->placeholder('Tambah tag...'),
                            ]),

                        Section::make('Gambar Utama')
                            ->description('Format otomatis dikonversi ke WebP')
                            ->schema([
                                FileUpload::make('gambar_utama')
                                    ->label('Berkas Gambar')
                                    ->image()
                                    ->disk('public')
                                    ->directory('berita')
                                    ->maxSize(5120)
                                    ->saveUploadedFileUsing(function ($file) {
                                        return app(ImageConversionService::class)->storeAsWebp($file, 'berita');
                                    }),

                                TextInput::make('alt_text')
                                    ->label('Teks Alternatif (Alt Text Aksesibilitas)')
                                    ->required(fn ($get) => !empty($get('gambar_utama')))
                                    ->placeholder('Deskripsi gambar untuk pembaca tuna netra (WCAG 2.1 AA)...')
                                    ->helperText('Wajib diisi jika gambar diunggah agar memenuhi standar aksesibilitas disabilitas netra.')
                                    ->maxLength(255),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('gambar_utama')
                    ->label('Foto')
                    ->disk('public')
                    ->square(),

                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'terbit' => 'success',
                        'terjadwal' => 'warning',
                        'draft' => 'gray',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('tanggal_terbit')
                    ->label('Waktu Terbit')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('unit.singkatan')
                    ->label('Unit / UPTD')
                    ->badge()
                    ->placeholder('Portal Utama')
                    ->visible(fn () => \App\Support\Package::has('subdomains')),

                TextColumn::make('views_count')
                    ->label('Dilihat')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('unit_id')
                    ->label('Unit / UPTD')
                    ->relationship('unit', 'nama')
                    ->visible(fn () => \App\Support\Package::has('subdomains') && (bool) auth()->user()?->hasRole('Super Admin')),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'terbit' => 'Terbit',
                        'terjadwal' => 'Terjadwal',
                    ]),
                SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'Pemerintahan' => 'Pemerintahan',
                        'Pembangunan' => 'Pembangunan',
                        'Kemasyarakatan' => 'Kemasyarakatan',
                        'Pengumuman' => 'Pengumuman',
                        'Kesehatan' => 'Kesehatan',
                        'Pendidikan' => 'Pendidikan',
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBeritas::route('/'),
            'create' => CreateBerita::route('/create'),
            'edit' => EditBerita::route('/{record}/edit'),
        ];
    }
}
