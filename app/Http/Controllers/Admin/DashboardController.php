<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dasbor admin.
     */
    public function index()
    {
        // Rekapitulasi ringkas untuk paket Silver
        $stats = [
            'total_permohonan' => 0,
            'permohonan_baru' => 0,
            'total_keberatan' => 0,
            'total_dip' => 0,
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
