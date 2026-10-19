<?php

namespace App\Filament\Resources\NavigationMenus;

use App\Filament\Resources\NavigationMenus\Pages\CreateNavigationMenu;
use App\Filament\Resources\NavigationMenus\Pages\EditNavigationMenu;
use App\Filament\Resources\NavigationMenus\Pages\ListNavigationMenus;
use App\Models\NavigationMenu;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NavigationMenuResource extends Resource
{
    protected static ?string $model = NavigationMenu::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bars-3';

    protected static string | \UnitEnum | null $navigationGroup = 'Tampilan & Navigasi';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Menu Navigasi Portal';

    protected static ?string $modelLabel = 'Menu Navigasi';

    protected static ?string $pluralModelLabel = 'Struktur Menu Navigasi';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Detail Menu Navigasi')
                            ->schema([
                                TextInput::make('nama')
                                    ->label('Nama Menu')
                                    ->placeholder('Contoh: Beranda, Profil, PPID, Layanan')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('url')
                                    ->label('Alamat Tautan (URL)')
                                    ->placeholder('Contoh: /, /profil, /ppid, atau https://...')
                                    ->required()
                                    ->maxLength(255),

                                Select::make('parent_id')
                                    ->label('Menu Induk (Opsional)')
                                    ->placeholder('Pilih jika menu ini adalah sub-menu (dropdown)')
                                    ->options(function (?NavigationMenu $record) {
                                        $query = NavigationMenu::query()->whereNull('parent_id');
                                        if ($record && $record->id) {
                                            $query->where('id', '!=', $record->id);
                                        }
                                        return $query->pluck('nama', 'id');
                                    })
                                    ->searchable()
                                    ->nullable(),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Pengaturan Menu')
                            ->schema([
                                TextInput::make('urutan')
                                    ->label('Urutan Tampil')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Angka lebih kecil tampil di urutan paling kiri/atas'),

                                Select::make('target')
                                    ->label('Target Jendela')
                                    ->options([
                                        '_self' => 'Jendela Saat Ini (_self)',
                                        '_blank' => 'Tab Baru (_blank)',
                                    ])
                                    ->default('_self')
                                    ->required(),

                                Toggle::make('is_active')
                                    ->label('Aktifkan Menu')
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
                TextColumn::make('urutan')
                    ->label('Urutan')
                    ->badge()
                    ->sortable(),

                TextColumn::make('nama')
                    ->label('Nama Menu')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('url')
                    ->label('URL')
                    ->color('gray')
                    ->copyable(),

                TextColumn::make('parent.nama')
                    ->label('Menu Induk')
                    ->badge()
                    ->color('info')
                    ->placeholder('Menu Utama'),

                TextColumn::make('target')
                    ->label('Target')
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('urutan', 'asc')
            ->filters([
                SelectFilter::make('parent_id')
                    ->label('Menu Induk')
                    ->relationship('parent', 'nama'),
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
            'index' => ListNavigationMenus::route('/'),
            'create' => CreateNavigationMenu::route('/create'),
            'edit' => EditNavigationMenu::route('/{record}/edit'),
        ];
    }
}
