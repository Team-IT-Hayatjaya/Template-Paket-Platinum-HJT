<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengaturanRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna memiliki otorisasi untuk membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Dapatkan aturan validasi untuk formulir pengaturan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_instansi' => ['required', 'string', 'max:255'],
            'sub_nama_instansi' => ['nullable', 'string', 'max:255'],
            'nama_ppid' => ['required', 'string', 'max:255'],
            'warna_utama' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'warna_sekunder' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'email', 'max:100'],
            'jam_layanan' => ['nullable', 'string', 'max:1000'],
            'maklumat_pelayanan' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'file', 'image', 'mimes:webp,png,jpg,jpeg,svg', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:ico,png,webp', 'max:1024'],
        ];
    }

    /**
     * Pesan kesalahan validasi dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'nama_instansi.required' => 'Nama instansi wajib diisi.',
            'nama_ppid.required' => 'Nama unit PPID wajib diisi.',
            'warna_utama.required' => 'Warna utama tema wajib dipilih.',
            'warna_utama.regex' => 'Format kode warna utama harus berupa heksadesimal 6 digit (contoh: #1e3a8a).',
            'warna_sekunder.regex' => 'Format kode warna sekunder harus berupa heksadesimal 6 digit (contoh: #b91c1c).',
            'email.email' => 'Format alamat email tidak valid.',
            'logo.image' => 'Berkas logo harus berupa gambar yang valid.',
            'logo.mimes' => 'Format berkas logo harus WebP, PNG, JPG, JPEG, atau SVG.',
            'logo.max' => 'Ukuran berkas logo maksimal 2 MB.',
            'favicon.mimes' => 'Format berkas favicon harus ICO, PNG, atau WebP.',
            'favicon.max' => 'Ukuran berkas favicon maksimal 1 MB.',
        ];
    }
}
