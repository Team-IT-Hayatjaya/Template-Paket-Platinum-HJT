<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Services\OpenDataService;
use App\Support\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DatasetApiController extends Controller
{
    public function __construct(
        protected OpenDataService $openDataService
    ) {}

    /**
     * API Daftar Dataset Terbuka Publik (Read-Only).
     */
    public function index(Request $request): JsonResponse
    {
        if (! Package::has('open_data')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan Open Data tidak diaktifkan pada paket sistem saat ini.',
            ], 403);
        }

        $query = Dataset::published()
            ->with('unit')
            ->orderByDesc('tanggal_pembaruan');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->query('kategori'));
        }

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%")
                    ->orWhere('sumber', 'like', "%{$q}%");
            });
        }

        $perPage = min(50, max(5, (int) $request->query('limit', 15)));
        $paginated = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'package' => Package::current(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
            ],
            'data' => $paginated->map(fn (Dataset $item) => [
                'id' => $item->id,
                'judul' => $item->judul,
                'slug' => $item->slug,
                'kategori' => $item->kategori,
                'sumber' => $item->sumber,
                'periode' => $item->periode,
                'penanggung_jawab' => $item->penanggung_jawab,
                'tanggal_pembaruan' => $item->tanggal_pembaruan?->toDateString(),
                'version' => $item->version,
                'row_count' => $item->row_count,
                'unit' => $item->unit?->singkatan ?? ($item->unit?->nama ?? 'Portal Utama'),
                'links' => [
                    'detail' => route('open-data.detail', $item->slug),
                    'api' => url("/api/v1/datasets/{$item->slug}"),
                    'download_csv' => route('open-data.download', ['slug' => $item->slug, 'format' => 'csv']),
                    'download_json' => route('open-data.download', ['slug' => $item->slug, 'format' => 'json']),
                ],
            ]),
        ]);
    }

    /**
     * API Detail Dataset beserta Data JSON (Read-Only).
     */
    public function show(string $slug): JsonResponse
    {
        if (! Package::has('open_data')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan Open Data tidak diaktifkan pada paket sistem saat ini.',
            ], 403);
        }

        $dataset = Dataset::published()
            ->with(['unit', 'versions'])
            ->where('slug', $slug)
            ->first();

        if (! $dataset) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dataset tidak ditemukan.',
            ], 404);
        }

        $dataset->increment('total_views');
        $exported = $this->openDataService->exportJson($dataset);

        return response()->json([
            'status' => 'success',
            'metadata' => $exported['metadata'],
            'schema' => $exported['schema'],
            'chart_config' => [
                'type' => $dataset->chart_type,
                'x_axis' => $dataset->chart_x_column,
                'y_axis' => $dataset->chart_y_column,
            ],
            'versions' => $dataset->versions->map(fn ($v) => [
                'version' => $v->version,
                'catatan' => $v->catatan_perubahan,
                'row_count' => $v->row_count,
                'tanggal' => $v->created_at?->toDateString(),
            ]),
            'data' => $exported['data'],
        ]);
    }
}
