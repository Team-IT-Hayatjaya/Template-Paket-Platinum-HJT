<?php

namespace App\Http\Middleware;

use App\Facades\UnitContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectUnit
{
    /**
     * Tangani request dan tentukan konteks Unit jika request mengakses subdomain UPTD/Bidang.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $unit = UnitContext::resolveFromRequest($request);

        if ($unit) {
            UnitContext::set($unit);
            view()->share('currentUnit', $unit);
        } else {
            UnitContext::reset();
            view()->share('currentUnit', null);
        }

        return $next($request);
    }
}
