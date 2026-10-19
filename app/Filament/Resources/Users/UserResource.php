<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Keamanan & Pengguna';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Kelola Pengguna & Peran';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Daftar Pengguna Panel Admin';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Informasi Akun Aparatur')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label('Alamat Email Resmi')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('password')
                                    ->label('Kata Sandi')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->helperText('Kosongkan jika tidak ingin mengubah kata sandi.'),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Hak Akses & Otoritas')
                            ->schema([
                                Select::make('roles')
                                    ->label('Peran / Role')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->required()
                                    ->helperText('Tentukan wewenang aparatur dalam sistem.'),

                                Select::make('unit_id')
                                    ->label('Unit / UPTD Penugasan')
                                    ->relationship('unit', 'nama')
                                    ->placeholder('Tidak Terikat Unit (Portal Induk)')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn () => \App\Support\Package::has('subdomains'))
                                    ->helperText('Pilih unit UPTD/Bidang jika pengguna berperan sebagai Admin Unit.'),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('roles.name')
                    ->label('Peran (Role)')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Super Admin' => 'danger',
                        'Atasan PPID' => 'warning',
                        'Petugas PPID' => 'info',
                        'Admin Konten' => 'success',
                        'Admin Unit' => 'primary',
                        default => 'secondary',
                    }),

                TextColumn::make('unit.singkatan')
                    ->label('Unit / UPTD')
                    ->badge()
                    ->placeholder('Portal Induk')
                    ->visible(fn () => \App\Support\Package::has('subdomains')),

                IconColumn::make('two_factor_secret')
                    ->label('2FA Aktif')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->two_factor_secret)),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
