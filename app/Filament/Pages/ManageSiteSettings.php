<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class ManageSiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Identitas & Pengaturan Web';

    protected static ?string $title = 'Identitas Instansi & Pengaturan Website';

    protected static string | \UnitEnum | null $navigationGroup = 'Pengaturan Instansi';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.manage-site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = SiteSetting::current();
        $this->form->fill($setting->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Identitas Instansi')
                    ->description('Nama dan identitas visual instansi pemerintah')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('institution_name')
                                ->label('Nama Instansi')
                                ->placeholder('Contoh: Pemerintah Kecamatan Loa Kulu')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('institution_acronym')
                                ->label('Singkatan / Sebutan Singkat')
                                ->placeholder('Contoh: Kec. Loa Kulu')
                                ->maxLength(100),
                        ]),
                        Grid::make(2)->schema([
                            FileUpload::make('logo_path')
                                ->label('Logo Resmi')
                                ->image()
                                ->directory('branding')
                                ->disk('public')
                                ->maxSize(2048),
                            FileUpload::make('favicon_path')
                                ->label('Favicon')
                                ->image()
                                ->directory('branding')
                                ->disk('public')
                                ->maxSize(1024),
                        ]),
                    ]),

                Section::make('Warna & Tema Portal')
                    ->description('Warna dasar yang digunakan pada portal publik')
                    ->schema([
                        Grid::make(2)->schema([
                            ColorPicker::make('primary_color')
                                ->label('Warna Utama (Primary)')
                                ->required(),
                            ColorPicker::make('secondary_color')
                                ->label('Warna Sekunder / Aksen')
                                ->required(),
                        ]),
                    ]),

                Section::make('Kontak & Layanan')
                    ->description('Alamat kantor, nomor kontak, dan jam operasional')
                    ->schema([
                        Textarea::make('address')
                            ->label('Alamat Lengkap Kantor')
                            ->rows(3),
                        Grid::make(2)->schema([
                            TextInput::make('phone')
                                ->label('Nomor Telepon / WhatsApp')
                                ->tel(),
                            TextInput::make('email')
                                ->label('Email Resmi')
                                ->email(),
                        ]),
                        Textarea::make('service_hours')
                            ->label('Jam Layanan Publik')
                            ->placeholder("Senin - Kamis: 08.00 - 16.00 WITA\nJumat: 08.00 - 15.30 WITA")
                            ->rows(3),
                    ]),

                Section::make('Media Sosial Resmi')
                    ->description('Tautan ke akun media sosial instansi')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('social_media.facebook')
                                ->label('Facebook')
                                ->url(),
                            TextInput::make('social_media.instagram')
                                ->label('Instagram')
                                ->url(),
                            TextInput::make('social_media.twitter')
                                ->label('Twitter / X')
                                ->url(),
                            TextInput::make('social_media.youtube')
                                ->label('YouTube')
                                ->url(),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $setting = SiteSetting::current();
        $setting->update($state);

        Notification::make()
            ->title('Pengaturan Berhasil Disimpan')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save'),
        ];
    }
}
