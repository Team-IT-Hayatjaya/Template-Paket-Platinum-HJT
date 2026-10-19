<?php

namespace App\Filament\Pages;

use App\Models\PengaturanPpid;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class ManagePengaturanPpid extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Pengaturan PPID & Maklumat';

    protected static ?string $title = 'Pengaturan Pelayanan Informasi Publik (PPID)';

    protected static string | \UnitEnum | null $navigationGroup = 'Layanan PPID';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.manage-site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = PengaturanPpid::current();
        $this->form->fill($setting->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Identitas Layanan PPID')
                    ->description('Data kelembagaan Pejabat Pengelola Informasi dan Dokumentasi sesuai UU No. 14/2008')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nama_ppid')
                                ->label('Sebutan / Nama PPID')
                                ->placeholder('Contoh: PPID Pelaksana Kecamatan Loa Kulu')
                                ->required(),

                            TextInput::make('kode_instansi')
                                ->label('Kode Singkatan Instansi')
                                ->placeholder('Contoh: LK')
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('email')
                                ->label('Email Resmi PPID')
                                ->email()
                                ->required(),

                            TextInput::make('email_atasan_ppid')
                                ->label('Email Atasan PPID / Sekretaris')
                                ->email()
                                ->placeholder('atasan.ppid@kukar.go.id'),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('telepon')
                                ->label('Nomor Telepon Layanan PPID')
                                ->tel(),

                            TextInput::make('jam_layanan')
                                ->label('Jadwal / Jam Layanan Permohonan')
                                ->placeholder('Senin - Jumat: 08.00 - 15.30 WITA'),
                        ]),
                    ]),

                Section::make('Maklumat Pelayanan Informasi Publik')
                    ->description('Pernyataan komitmen resmi aparatur dalam memberikan pelayanan informasi')
                    ->schema([
                        Textarea::make('maklumat_pelayanan')
                            ->label('Teks Maklumat Pelayanan')
                            ->rows(5)
                            ->placeholder('Kami berjanji dan berkomitmen memberikan pelayanan informasi publik yang cepat, tepat waktu, biaya ringan, dan proporsional...'),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan PPID')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $setting = PengaturanPpid::current();
        $setting->update($data);

        Notification::make()
            ->title('Pengaturan PPID Disimpan')
            ->body('Data identitas dan maklumat PPID berhasil diperbarui.')
            ->success()
            ->send();
    }
}
