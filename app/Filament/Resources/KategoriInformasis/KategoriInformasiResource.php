<?php

namespace App\Filament\Resources\KategoriInformasis;

use App\Filament\Resources\KategoriInformasis\Pages\CreateKategoriInformasi;
use App\Filament\Resources\KategoriInformasis\Pages\EditKategoriInformasi;
use App\Filament\Resources\KategoriInformasis\Pages\ListKategoriInformasis;
use App\Models\KategoriInformasi;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class KategoriInformasiResource extends Resource
{
    protected static ?string $model = KategoriInformasi::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static string | \UnitEnum | null $navigationGroup = 'Layanan PPID';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Kategori Informasi';

    protected static ?string $modelLabel = 'Kategori Informasi';

    protected static ?string $pluralModelLabel = 'Kategori Informasi Publik';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rincian Kategori PPID')
                    ->description('Kategori klasifikasi dokumen sesuai UU No. 14/2008 (Berkala, Setiap Saat, Serta Merta, Dikecualikan)')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nama')
                                ->label('Nama Kategori')
                                ->placeholder('Contoh: Informasi Berkala')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                            TextInput::make('slug')
                                ->label('Slug URL')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                        ]),

                        Textarea::make('deskripsi')
                            ->label('Deskripsi Klasifikasi')
                            ->placeholder('Jelaskan cakupan dokumen yang tergolong dalam kategori ini...')
                            ->rows(3),

                        Grid::make(2)->schema([
                            TextInput::make('urutan')
                                ->label('Nomor Urut Tampilan')
                                ->numeric()
                                ->default(0),

                            Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true)
                                ->inline(false),
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
                    ->sortable(),

                TextColumn::make('nama')
                    ->label('Kategori Informasi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->color('gray'),

                TextColumn::make('dokumen_count')
                    ->label('Jumlah Dokumen')
                    ->counts('dokumen')
                    ->badge()
                    ->color('primary'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('urutan')
            ->actions([
                EditAction::make(),
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
            'index' => ListKategoriInformasis::route('/'),
            'create' => CreateKategoriInformasi::route('/create'),
            'edit' => EditKategoriInformasi::route('/{record}/edit'),
        ];
    }
}
