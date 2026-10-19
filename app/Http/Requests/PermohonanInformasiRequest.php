<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermohonanInformasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_pemohon' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:1000'],
            'telepon' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'nik' => ['nullable', 'string', 'max:30'],
            'rincian_informasi' => ['required', 'string', 'max:3000'],
            'tujuan_penggunaan' => ['required', 'string', 'max:1000'],
            'cara_memperoleh_informasi' => ['required', 'string', 'in:melihat,salinan,soft_copy'],
            'cara_mendapat_salinan' => ['nullable', 'string', 'in:langsung,pos,email,soft_copy'],
            'identitas_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia yang baku dan informatif.
     */
    public function messages(): array
    {
        return [
            'nama_pemohon.required' => 'Nama lengkap pemohon wajib diisi.',
            'alamat.required' => 'Alamat tempat tinggal pemohon wajib diisi.',
            'telepon.required' => 'Nomor telepon / kontak yang dapat dihubungi wajib diisi.',
            'email.required' => 'Alamat email aktif wajib diisi untuk pengiriman bukti registrasi dan jawaban.',
            'email.email' => 'Format alamat email tidak valid.',
            'rincian_informasi.required' => 'Rincian informasi publik yang diminta wajib diisi dengan jelas.',
            'tujuan_penggunaan.required' => 'Tujuan penggunaan informasi wajib diuraikan.',
            'cara_memperoleh_informasi.required' => 'Cara memperoleh informasi wajib dipilih.',
            'cara_mendapat_salinan.required' => 'Bentuk atau media salinan wajib dipilih.',
            'identitas_file.mimes' => 'Berkas identitas harus berformat PDF, JPG, JPEG, PNG, atau WEBP.',
            'identitas_file.max' => 'Ukuran berkas identitas maksimal 5 MB.',
        ];
    }
}
