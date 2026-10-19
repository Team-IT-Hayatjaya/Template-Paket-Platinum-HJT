<?php

namespace App\Filament\Resources\Galeris;

use App\Filament\Resources\Galeris\Pages\CreateGaleri;
use App\Filament\Resources\Galeris\Pages\EditGaleri;
use App\Filament\Resources\Galeris\Pages\ListGaleris;
use App\Models\Galeri;
use App\Services\ImageConversionService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class GaleriResource extends Resource
{
    protected static ?string $model = Galeri::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-photo';

    protected static string | \UnitEnum | null $navigationGroup = 'Publikasi & Informasi';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Galeri Foto & Dokumentasi';

    protected static ?string $modelLabel = 'Galeri';

    protected static ?string $pluralModelLabel = 'Galeri Foto & Infografis';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Informasi Media')
                            ->schema([
                                TextInput::make('judul')
                                    ->label('Judul Foto / Infografis')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                    }),

                                TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->required()
                                    ->unique(Galeri::class, 'slug', ignoreRecord: true)
                                    ->maxLength(255),

                                Textarea::make('deskripsi')
                                    ->label('Deskripsi / Keterangan')
                                    ->rows(3)
                                    ->maxLength(500),

                                FileUpload::make('gambar')
                                    ->label('Berkas Foto / Infografis')
                                    ->image()
                                    ->required()
                                    ->disk('public')
                                    ->directory('galeri')
                                    ->maxSize(5120)
                                    ->helperText('Format gambar otomatis dikonversi menjadi WebP')
                                    ->saveUploadedFileUsing(function ($file) {
                                        return app(ImageConversionService::class)->storeAsWebp($file, 'galeri');
                                    }),

                                TextInput::make('alt_text')
                                    ->label('Teks Alternatif (Alt Text Aksesibilitas)')
                                    ->required()
                                    ->placeholder('Deskripsi gambar untuk pembaca layar tuna netra...')
                                    ->helperText('Wajib diisi sesuai standar aksesibilitas WCAG 2.1 AA.')
                                    ->maxLength(255),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Kategori & Publikasi')
                            ->schema([
                                Radio::make('tipe')
                                    ->label('Tipe Media')
                                    ->options([
                                        'foto' => 'Foto Dokumentasi',
                                        'infografis' => 'Infografis Publik',
                                    ])
                                    ->default('foto')
                                    ->required(),

                                Select::make('kategori')
                                    ->label('Kategori')
                                    ->options([
                                        'Kegiatan' => 'Kegiatan Kedinasan',
                                        'Pembangunan' => 'Infrastruktur & Pembangunan',
                                        'Sosial' => 'Sosial & Kemasyarakatan',
                                        'Layanan' => 'Pelayanan Publik',
                                        'Umum' => 'Umum',
                                    ])
                                    ->default('Kegiatan')
                                    ->required(),

                                DatePicker::make('tanggal')
                                    ->label('Tanggal Kegiatan / Media')
                                    ->default(now())
                                    ->required(),

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
                ImageColumn::make('gambar')
                    ->label('Pratinjau')
                    ->disk('public')
                    ->square(),

                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'infografis' => 'warning',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                SelectFilter::make('tipe')
                    ->label('Tipe Media')
                    ->options([
                        'foto' => 'Foto Dokumentasi',
                        'infografis' => 'Infografis',
                    ]),
                SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'Kegiatan' => 'Kegiatan',
                        'Pembangunan' => 'Pembangunan',
                        'Sosial' => 'Sosial',
                        'Layanan' => 'Layanan',
                        'Umum' => 'Umum',
                    ]),
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
            'index' => ListGaleris::route('/'),
            'create' => CreateGaleri::route('/create'),
            'edit' => EditGaleri::route('/{record}/edit'),
        ];
    }
}
