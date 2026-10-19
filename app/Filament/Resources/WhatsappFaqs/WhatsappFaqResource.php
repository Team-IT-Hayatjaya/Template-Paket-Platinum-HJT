<?php

namespace App\Filament\Resources\WhatsappFaqs;

use App\Filament\Resources\WhatsappFaqs\Pages\CreateWhatsappFaq;
use App\Filament\Resources\WhatsappFaqs\Pages\EditWhatsappFaq;
use App\Filament\Resources\WhatsappFaqs\Pages\ListWhatsappFaqs;
use App\Models\WhatsappFaq;
use App\Support\Package;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WhatsappFaqResource extends Resource
{
    protected static ?string $model = WhatsappFaq::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static string | \UnitEnum | null $navigationGroup = 'Pelayanan Warga';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Tanya Jawab Bot (FAQ)';

    protected static ?string $modelLabel = 'FAQ Bot WhatsApp';

    protected static ?string $pluralModelLabel = 'Daftar Tanya Jawab WhatsApp Bot';

    public static function canAccess(): bool
    {
        return Package::has('whatsapp_bot') && (auth()->user()?->hasAnyRole(['Super Admin', 'Admin Konten', 'Petugas PPID', 'Atasan PPID']) ?? false);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Konten Tanya Jawab')
                            ->schema([
                                TextInput::make('kata_kunci')
                                    ->label('Kata Kunci Pemicu')
                                    ->placeholder('Contoh: syarat, biaya, waktu, lokasi')
                                    ->helperText('Kata kunci singkat (tanpa spasi) yang akan dicocokkan dari pesan pemohon')
                                    ->required()
                                    ->maxLength(100),

                                TextInput::make('pertanyaan')
                                    ->label('Judul / Inti Pertanyaan')
                                    ->placeholder('Contoh: Apa saja syarat pengajuan informasi?')
                                    ->required()
                                    ->maxLength(255),

                                Textarea::make('jawaban')
                                    ->label('Teks Jawaban Bot')
                                    ->placeholder('Tuliskan jawaban lengkap yang akan dikirim bot...')
                                    ->rows(6)
                                    ->required(),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Pengaturan Tampilan')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),

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
                TextColumn::make('kata_kunci')
                    ->label('Kata Kunci')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pertanyaan')
                    ->label('Pertanyaan')
                    ->limit(50)
                    ->searchable()
                    ->weight('bold'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('urutan')
                    ->label('Urutan')
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
            'index' => ListWhatsappFaqs::route('/'),
            'create' => CreateWhatsappFaq::route('/create'),
            'edit' => EditWhatsappFaq::route('/{record}/edit'),
        ];
    }
}
