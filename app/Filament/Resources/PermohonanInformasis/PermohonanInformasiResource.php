<?php

namespace App\Filament\Resources\PermohonanInformasis;

use App\Filament\Resources\PermohonanInformasis\Pages\CreatePermohonanInformasi;
use App\Filament\Resources\PermohonanInformasis\Pages\EditPermohonanInformasi;
use App\Filament\Resources\PermohonanInformasis\Pages\ListPermohonanInformasis;
use App\Models\PermohonanInformasi;
use App\Models\StatusPermohonan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class PermohonanInformasiResource extends Resource
{
    protected static ?string $model = PermohonanInformasi::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static string | \UnitEnum | null $navigationGroup = 'Layanan PPID';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Permohonan Informasi';

    protected static ?string $modelLabel = 'Permohonan Informasi';

    protected static ?string $pluralModelLabel = 'Daftar Permohonan Informasi Publik';

    public static function getNavigationBadge(): ?string
    {
        $count = PermohonanInformasi::whereHas('status', function ($query) {
            $query->whereIn('slug', ['menunggu', 'diproses']);
        })->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Uraian Permohonan Informasi')
                            ->schema([
                                Textarea::make('rincian_informasi')
                                    ->label('Rincian Informasi yang Diminta')
                                    ->disabled()
                                    ->rows(3),

                                Textarea::make('tujuan_penggunaan')
                                    ->label('Tujuan Penggunaan Informasi')
                                    ->disabled()
                                    ->rows(2),

                                Grid::make(2)->schema([
                                    TextInput::make('cara_memperoleh_informasi')
                                        ->label('Cara Memperoleh')
                                        ->disabled(),
                                    TextInput::make('cara_mendapat_salinan')
                                        ->label('Bentuk / Media Salinan')
                                        ->disabled(),
                                ]),
                            ]),

                        Section::make('Pemrosesan & Tanggapan Resmi Petugas PPID')
                            ->description('Catat perkembangan tindak lanjut, ubah status, dan unggah jawaban resmi')
                            ->schema([
                                Select::make('status_id')
                                    ->label('Status Permohonan')
                                    ->options(fn () => StatusPermohonan::pluck('nama', 'id'))
                                    ->required()
                                    ->helperText('Perubahan status ke Dijawab/Ditolak akan memicu notifikasi email resmi ke pemohon'),

                                Textarea::make('jawaban_teks')
                                    ->label('Uraian Tanggapan / Keterangan Resmi')
                                    ->placeholder('Tuliskan tanggapan tertulis resmi dari PPID kepada pemohon...')
                                    ->rows(4),

                                FileUpload::make('jawaban_file_path')
                                    ->label('Unggah Berkas Dokumen Jawaban (PDF)')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->disk('private')
                                    ->directory('jawaban')
                                    ->maxSize(10240)
                                    ->helperText('Format PDF, maksimal 10 MB. Otomatis dilampirkan pada email jawaban'),

                                Textarea::make('catatan_internal')
                                    ->label('Catatan Internal PPID')
                                    ->placeholder('Catatan koordinasi internal dinas/unit pengampu (hanya dapat dilihat oleh admin & petugas PPID)...')
                                    ->rows(3),
                            ]),
                    ]),

                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Identitas Pemohon')
                            ->schema([
                                TextInput::make('nomor_registrasi')
                                    ->label('Nomor Registrasi')
                                    ->disabled(),

                                TextInput::make('nama_pemohon')
                                    ->label('Nama Lengkap')
                                    ->disabled(),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->disabled(),

                                TextInput::make('telepon')
                                    ->label('Telepon / WhatsApp')
                                    ->disabled(),

                                TextInput::make('pekerjaan')
                                    ->label('Pekerjaan')
                                    ->disabled(),

                                Textarea::make('alamat')
                                    ->label('Alamat Pemohon')
                                    ->disabled()
                                    ->rows(2),

                                Placeholder::make('berkas_identitas')
                                    ->label('Berkas Identitas KTP / SK')
                                    ->content(function (?PermohonanInformasi $record) {
                                        if (!$record || empty($record->identitas_file_path)) {
                                            return new HtmlString('<span class="text-xs text-gray-500 italic">Tidak ada berkas identitas yang diunggah.</span>');
                                        }

                                        $url = route('ppid.download-identitas', $record->id);
                                        return new HtmlString('<a href="' . $url . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition"><span>Unduh Berkas Privat</span></a>');
                                    }),
                            ]),

                        Section::make('Tenggat Waktu Respons (UU KIP)')
                            ->schema([
                                DateTimePicker::make('tenggat_jawab')
                                    ->label('Batas Akhir Respons (10 Hari Kerja)')
                                    ->disabled(),

                                Placeholder::make('status_tenggat_info')
                                    ->label('Status Kepatuhan Tenggat')
                                    ->content(function (?PermohonanInformasi $record) {
                                        if (!$record) {
                                            return '-';
                                        }
                                        $label = $record->tenggat_badge_label;
                                        $color = match ($record->tenggat_badge_color) {
                                            'danger' => 'color: #dc2626;',
                                            'warning' => 'color: #d97706;',
                                            'success' => 'color: #16a34a;',
                                            default => 'color: #2563eb;',
                                        };
                                        return new HtmlString("<strong style=\"{$color}\">{$label}</strong>");
                                    }),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_registrasi')
                    ->label('No. Registrasi')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable(),

                TextColumn::make('nama_pemohon')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email & Kontak')
                    ->description(fn (PermohonanInformasi $record): string => $record->telepon ?? '-')
                    ->searchable(),

                TextColumn::make('status.nama')
                    ->label('Status')
                    ->badge()
                    ->color(fn (PermohonanInformasi $record): string => match ($record->status?->slug) {
                        'diterima' => 'info',
                        'diverifikasi' => 'primary',
                        'diproses' => 'warning',
                        'dijawab', 'selesai' => 'success',
                        'ditolak' => 'danger',
                        default => 'secondary',
                    }),

                TextColumn::make('tenggat_badge_label')
                    ->label('Tenggat 10 Hari Kerja')
                    ->badge()
                    ->color(fn (PermohonanInformasi $record): string => $record->tenggat_badge_color),

                TextColumn::make('tenggat_jawab')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tgl Masuk')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status_id')
                    ->label('Filter Status')
                    ->relationship('status', 'nama'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Tindak Lanjut'),
                Action::make('unduh_identitas')
                    ->label('Identitas')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('secondary')
                    ->visible(fn (PermohonanInformasi $record): bool => !empty($record->identitas_file_path))
                    ->url(fn (PermohonanInformasi $record): string => route('ppid.download-identitas', $record->id))
                    ->openUrlInNewTab(),
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
            'index' => ListPermohonanInformasis::route('/'),
            'create' => CreatePermohonanInformasi::route('/create'),
            'edit' => EditPermohonanInformasi::route('/{record}/edit'),
        ];
    }
}
