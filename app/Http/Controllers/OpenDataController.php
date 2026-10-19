<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Services\OpenDataService;
use App\Support\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OpenDataController extends Controller
{
    public function __construct(
        protected OpenDataService $openDataService
    ) {}

    /**
     * Halaman Publik Daftar Dataset Terbuka.
     */
    public function index(Request $request)
    {
        if (! Package::has('open_data')) {
            abort(404, 'Layanan Open Data tidak aktif pada paket ini.');
        }

        $q = $request->query('q');
        $kategori = $request->query('kategori');
        $format = $request->query('format');

        $query = Dataset::published()
            ->with('unit')
            ->orderByDesc('tanggal_pembaruan')
            ->orderByDesc('created_at');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%")
                    ->orWhere('sumber', 'like', "%{$q}%")
                    ->orWhere('penanggung_jawab', 'like', "%{$q}%");
            });
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        $datasets = $query->paginate(12)->withQueryString();

        $kategoriList = Dataset::published()
            ->select('kategori')
            ->distinct()
            ->pluck('kategori')
            ->filter()
            ->values();

        $totalDatasets = Dataset::published()->count();
        $totalUnduhan = Dataset::published()->sum('total_unduhan');

        return view('pages.open-data.index', compact('datasets', 'kategoriList', 'kategori', 'q', 'totalDatasets', 'totalUnduhan'));
    }

    /**
     * Halaman Detail Dataset & Visualisasi Interaktif (Chart.js).
     */
    public function show(string $slug)
    {
        if (! Package::has('open_data')) {
            abort(404, 'Layanan Open Data tidak aktif pada paket ini.');
        }

        $dataset = Dataset::published()
            ->with(['unit', 'versions'])
            ->where('slug', $slug)
            ->firstOrFail();

        $dataset->increment('total_views');

        $previewData = $this->openDataService->getDatasetData($dataset, 15);
        $chartConfig = $this->openDataService->getChartConfig($dataset);

        return view('pages.open-data.show', compact('dataset', 'previewData', 'chartConfig'));
    }

    /**
     * Unduh berkas dataset dalam format CSV atau JSON.
     */
    public function download(string $slug, Request $request)
    {
        if (! Package::has('open_data')) {
            abort(404, 'Layanan Open Data tidak aktif pada paket ini.');
        }

        $dataset = Dataset::published()->where('slug', $slug)->firstOrFail();
        $dataset->increment('total_unduhan');

        $format = strtolower((string) $request->query('format', 'csv'));

        if ($format === 'json') {
            $jsonData = $this->openDataService->exportJson($dataset);
            $fileName = "{$dataset->slug}-v{$dataset->version}.json";

            return response()->json($jsonData, 200, [
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ]);
        }

        // Unduh berkas CSV asli
        if (! Storage::disk('public')->exists($dataset->berkas_csv)) {
            abort(404, 'Berkas dataset tidak ditemukan.');
        }

        $fileName = "{$dataset->slug}-v{$dataset->version}.csv";
        return Storage::disk('public')->download($dataset->berkas_csv, $fileName);
    }
}
