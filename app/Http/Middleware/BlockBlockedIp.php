<?php

namespace App\Http\Middleware;

use App\Models\IpBlocklist;
use App\Support\Package;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tolak permintaan dari IP yang terdaftar pada daftar blokir (paket Gold).
 * Pada paket Silver middleware ini langsung meneruskan permintaan.
 */
class BlockBlockedIp
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Package::has('security_hardening')) {
            return $next($request);
        }

        try {
            $blocked = IpBlocklist::isBlocked($request->ip());
        } catch (\Throwable) {
            // Tabel belum dimigrasi (instalasi baru) — jangan memblokir apa pun.
            $blocked = false;
        }

        if ($blocked) {
            return response()->view('errors.ip-blocked', [], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
