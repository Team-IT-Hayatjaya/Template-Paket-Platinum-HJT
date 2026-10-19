<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class HealthCheckController extends Controller
{
    /**
     * Endpoint pemeriksaan kesehatan sistem (Database, Storage, dan Queue).
     */
    public function __invoke(Request $request)
    {
        $startTime = microtime(true);
        $checks = [];
        $isHealthy = true;

        // 1. Pemeriksaan Database
        try {
            $dbStart = microtime(true);
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            $dbDuration = round((microtime(true) - $dbStart) * 1000, 2);

            $checks['database'] = [
                'status' => 'ok',
                'message' => 'Koneksi database berhasil dan responsif.',
                'latency_ms' => $dbDuration,
                'connection' => config('database.default'),
            ];
        } catch (\Throwable $e) {
            $isHealthy = false;
            $checks['database'] = [
                'status' => 'error',
                'message' => 'Gagal terhubung ke database: ' . $e->getMessage(),
                'latency_ms' => null,
            ];
        }

        // 2. Pemeriksaan Penyimpanan (Storage)
        try {
            $publicWritable = is_writable(storage_path('app/public')) || Storage::disk('public')->put('.health_test', 'ok');
            $privateWritable = is_writable(storage_path('app/private')) || Storage::disk('private')->put('.health_test', 'ok');
            $frameworkWritable = is_writable(storage_path('framework'));

            // Cleanup test files
            Storage::disk('public')->delete('.health_test');
            Storage::disk('private')->delete('.health_test');

            if ($publicWritable && $privateWritable && $frameworkWritable) {
                $checks['storage'] = [
                    'status' => 'ok',
                    'message' => 'Direktori penyimpanan publik, privat, dan framework dapat ditulis.',
                    'disks' => ['public' => 'writable', 'private' => 'writable'],
                ];
            } else {
                $isHealthy = false;
                $checks['storage'] = [
                    'status' => 'error',
                    'message' => 'Sebagian direktori penyimpanan tidak memiliki izin tulis (writable).',
                    'disks' => [
                        'public' => $publicWritable ? 'writable' : 'unwritable',
                        'private' => $privateWritable ? 'writable' : 'unwritable',
                    ],
                ];
            }
        } catch (\Throwable $e) {
            $isHealthy = false;
            $checks['storage'] = [
                'status' => 'error',
                'message' => 'Gagal memeriksa status storage: ' . $e->getMessage(),
            ];
        }

        // 3. Pemeriksaan Antrean (Queue)
        try {
            $driver = config('queue.default');
            $queueOk = true;

            if ($driver === 'database') {
                $queueOk = Schema::hasTable('jobs');
            }

            if ($queueOk) {
                $checks['queue'] = [
                    'status' => 'ok',
                    'message' => "Driver antrean [{$driver}] siap beroperasi.",
                    'driver' => $driver,
                ];
            } else {
                $isHealthy = false;
                $checks['queue'] = [
                    'status' => 'error',
                    'message' => "Tabel antrean database 'jobs' belum tersedia.",
                    'driver' => $driver,
                ];
            }
        } catch (\Throwable $e) {
            $isHealthy = false;
            $checks['queue'] = [
                'status' => 'error',
                'message' => 'Gagal memeriksa driver antrean: ' . $e->getMessage(),
            ];
        }

        $totalDuration = round((microtime(true) - $startTime) * 1000, 2);
        $httpStatus = $isHealthy ? 200 : 503;

        $payload = [
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'total_latency_ms' => $totalDuration,
            'checks' => $checks,
            'package' => config('packages.default', 'silver'),
        ];

        // Jika request via API atau meminta JSON
        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json($payload, $httpStatus);
        }

        // Tampilan antarmuka HTML ramah pengguna
        return response()->view('pages.health', compact('payload', 'isHealthy'), $httpStatus);
    }
}
