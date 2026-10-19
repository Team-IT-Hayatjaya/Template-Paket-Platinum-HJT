<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageViews
{
    /**
     * Handle an incoming request and track public page views.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya catat respons sukses (200 OK) pada metode GET untuk halaman publik
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            $path = $request->path();

            // Abaikan rute panel admin, livewire, storage, health, dan aset
            if (!$this->shouldIgnore($path)) {
                try {
                    PageView::recordView('/' . ltrim($path, '/'), $request->userAgent());
                } catch (\Throwable $e) {
                    // Jangan sampai error pencatatan analitik memblokir halaman publik
                }
            }
        }

        return $response;
    }

    /**
     * Tentukan apakah path request harus diabaikan dari statistik.
     */
    protected function shouldIgnore(string $path): bool
    {
        $path = strtolower(trim($path, '/'));

        $ignoredPrefixes = [
            'admin',
            'livewire',
            'storage',
            'health',
            'up',
            'robots.txt',
            'sitemap.xml',
        ];

        foreach ($ignoredPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return true;
            }
        }

        // Abaikan file ekstensi umum jika ada
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|webp|pdf|map)$/i', $path)) {
            return true;
        }

        return false;
    }
}
