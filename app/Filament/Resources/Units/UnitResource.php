<?php

namespace App\Filament\Resources\Units;

use App\Filament\Resources\Units\Pages\CreateUnit;
use App\Filament\Resources\Units\Pages\EditUnit;
use App\Filament\Resources\Units\Pages\ListUnits;
use App\Models\Unit;
use App\Support\Package;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string | \UnitEnum | null $navigationGroup = 'Pengaturan Instansi';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Unit Kerja & UPTD';

    protected static ?string $modelLabel = 'Unit Kerja / UPTD';

    protected static ?string $pluralModelLabel = 'Unit Kerja & UPTD (Sub-domain)';

    public static function canAccess(): bool
    {
        return Package::has('subdomains') && (auth()->user()?->hasRole('Super Admin') ?? false);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Identitas Unit Kerja / UPTD')
                            ->schema([
                                TextInput::make('nama')
                                    ->label('Nama Lengkap Unit')
                                    ->placeholder('Contoh: Dinas Pemuda dan Olahraga')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                    }),

                                Grid::make(2)->schema([
                                    TextInput::make('slug')
                                        ->label('Slug')
                                        ->required()
                                        ->unique(Unit::class, 'slug', ignoreRecord: true)
                                        ->maxLength(255),

                                    TextInput::make('subdomain')
                                        ->label('Sub-domain Host')
                                        ->placeholder('Contoh: dispora')
                                        ->helperText('Akan diakses via https://[subdomain].domain-anda.go.id')
                                        ->required()
                                        ->regex('/^[a-z0-9\-]+$/')
                                        ->unique(Unit::class, 'subdomain', ignoreRecord: true)
                                        ->maxLength(60),
                                ]),

                                TextInput::make('singkatan')
                                    ->label('Singkatan / Akronim')
                                    ->placeholder('Contoh: DISPORA')
                                    ->maxLength(30),

                                Textarea::make('deskripsi')
                                    ->label('Deskripsi / Tugas Pokok')
                                    ->rows(3)
                                    ->maxLength(500),
                            ]),

                        Section::make('Kontak & Lokasi')
                            ->schema([
                                TextInput::make('alamat')
                                    ->label('Alamat Kantor')
                                    ->maxLength(255),

                                Grid::make(2)->schema([
                                    TextInput::make('email')
                                        ->label('Email Dinas')
                                        ->email()
                                        ->maxLength(255),

                                    TextInput::make('telepon')
                                        ->label('Nomor Telepon')
                                        ->tel()
                                        ->maxLength(50),
                                ]),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Tampilan & Pengaturan')
                            ->schema([
                                ColorPicker::make('warna_tema')
                                    ->label('Warna Aksen Tema')
                                    ->default('#1e40af'),

                                FileUpload::make('logo')
                                    ->label('Logo Unit (Opsional)')
                                    ->image()
                                    ->disk('public')
                                    ->directory('units/logos')
                                    ->maxSize(2048),

                                Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->helperText('Nonaktifkan jika sub-domain sementara ditutup.'),

                                TextInput::make('urutan')
                                    ->label('Urutan Tampilan')
                                    ->numeric()
                                    ->default(0),
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
                    ->label('Nama Unit')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Unit $record) => $record->singkatan ? "({$record->singkatan})" : null),

                TextColumn::make('subdomain')
                    ->label('Sub-domain')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => "{$state}." . request()->getHost())
                    ->copyable()
                    ->sortable(),

                ColorColumn::make('warna_tema')
                    ->label('Aksen'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('berita_count')
                    ->counts('berita')
                    ->label('Berita')
                    ->sortable(),

                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Admin')
                    ->sortable(),
            ])
            ->defaultSort('urutan')
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
            'index' => ListUnits::route('/'),
            'create' => CreateUnit::route('/create'),
            'edit' => EditUnit::route('/{record}/edit'),
        ];
    }
}
