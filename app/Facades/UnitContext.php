<?php

namespace App\Facades;

use App\Services\UnitContextService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\Unit|null current()
 * @method static void set(\App\Models\Unit|null $unit)
 * @method static bool hasUnit()
 * @method static int|null unitId()
 * @method static void reset()
 * @method static \App\Models\Unit|null resolveFromRequest(\Illuminate\Http\Request $request)
 *
 * @see \App\Services\UnitContextService
 */
class UnitContext extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return UnitContextService::class;
    }
}
