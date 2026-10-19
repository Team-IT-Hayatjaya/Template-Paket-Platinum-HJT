<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePengaturanRequest;
use App\Services\PengaturanService;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function __construct(
        protected PengaturanService $pengaturanService
    ) {}

    /**
     * Tampilkan formulir pengaturan instansi dan tema.
     */
    public function edit()
    {
        $pengaturan = $this->pengaturanService->getPengaturan();

        return view('admin.pengaturan.edit', compact('pengaturan'));
    }

    /**
     * Simpan pembaruan pengaturan instansi dan tema.
     */
    public function update(UpdatePengaturanRequest $request)
    {
        $validated = $request->validated();

        $this->pengaturanService->updatePengaturan(
            $validated,
            $request->file('logo'),
            $request->file('favicon')
        );

        return redirect()->route('admin.pengaturan.edit')
            ->with('success', 'Pengaturan instansi dan variabel tema berhasil diperbarui.');
    }
}
