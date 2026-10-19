<?php

namespace App\Filament\Resources\Banners;

use App\Filament\Resources\Banners\Pages\CreateBanner;
use App\Filament\Resources\Banners\Pages\EditBanner;
use App\Filament\Resources\Banners\Pages\ListBanners;
use App\Models\Banner;
use App\Services\ImageConversionService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';

    protected static string | \UnitEnum | null $navigationGroup = 'Tampilan & Navigasi';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Banner & Pengumuman';

    protected static ?string $modelLabel = 'Banner';

    protected static ?string $pluralModelLabel = 'Banner Beranda & Pengumuman';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Informasi Banner')
                            ->schema([
                                TextInput::make('judul')
                                    ->label('Judul Banner')
                                    ->placeholder('Contoh: Selamat Datang di Portal Resmi Loa Kulu')
                                    ->required()
                                    ->maxLength(255),

                                Textarea::make('deskripsi')
                                    ->label('Subjudul / Deskripsi Singkat')
                                    ->rows(3)
                                    ->maxLength(500),

                                Grid::make(2)->schema([
                                    TextInput::make('tautan')
                                        ->label('Tautan Aksi (URL)')
                                        ->placeholder('https:// atau /halaman/...'),

                                    TextInput::make('label_tautan')
                                        ->label('Label Tombol Aksi')
                                        ->placeholder('Contoh: Lihat Layanan, Unduh Sekarang'),
                                ]),

                                FileUpload::make('gambar')
                                    ->label('Gambar Banner / Slider')
                                    ->image()
                                    ->disk('public')
                                    ->directory('banners')
                                    ->maxSize(5120)
                                    ->helperText('Gambar otomatis dikonversi ke format WebP')
                                    ->saveUploadedFileUsing(function ($file) {
                                        return app(ImageConversionService::class)->storeAsWebp($file, 'banners');
                                    }),

                                TextInput::make('alt_text')
                                    ->label('Teks Alternatif (Alt Text Aksesibilitas)')
                                    ->required(fn ($get) => !empty($get('gambar')))
                                    ->placeholder('Deskripsi visual gambar banner untuk pembaca tuna netra...')
                                    ->helperText('Wajib diisi sesuai standar WCAG 2.1 AA.')
                                    ->maxLength(255),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Tipe & Posisi')
                            ->schema([
                                Radio::make('tipe')
                                    ->label('Tipe Banner')
                                    ->options([
                                        'slider' => 'Hero Slider Beranda',
                                        'pengumuman' => 'Banner Pengumuman',
                                        'darurat' => 'Peringatan Darurat / Siaga',
                                    ])
                                    ->default('slider')
                                    ->required(),

                                TextInput::make('urutan')
                                    ->label('Urutan Tampil')
                                    ->numeric()
                                    ->default(0),

                                Toggle::make('is_active')
                                    ->label('Aktif / Ditampilkan')
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
                    ->label('Banner')
                    ->disk('public'),

                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),

                TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'slider' => 'primary',
                        'pengumuman' => 'warning',
                        'darurat' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('urutan')
                    ->label('Urutan')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('urutan', 'asc')
            ->filters([
                SelectFilter::make('tipe')
                    ->label('Tipe Banner')
                    ->options([
                        'slider' => 'Hero Slider',
                        'pengumuman' => 'Pengumuman',
                        'darurat' => 'Darurat',
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBanners::route('/'),
            'create' => CreateBanner::route('/create'),
            'edit' => EditBanner::route('/{record}/edit'),
        ];
    }
}
