<?php

namespace App\Models\Traits;

use App\Facades\UnitContext;
use App\Models\Scopes\UnitScope;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToUnit
{
    /**
     * Boot trait BelongsToUnit.
     */
    public static function bootBelongsToUnit(): void
    {
        static::addGlobalScope(new UnitScope());

        static::creating(function (Model $model) {
            if (empty($model->unit_id)) {
                if (UnitContext::hasUnit()) {
                    $model->unit_id = UnitContext::unitId();
                } elseif (auth()->check() && auth()->user()->hasRole('Admin Unit') && auth()->user()->unit_id) {
                    $model->unit_id = auth()->user()->unit_id;
                }
            }
        });
    }

    /**
     * Relasi ke Unit / UPTD pemilik konten.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Cek apakah konten milik Portal Utama (Induk).
     */
    public function isMainPortal(): bool
    {
        return $this->unit_id === null;
    }

    /**
     * Scope untuk mengambil seluruh konten tanpa filter UnitScope.
     */
    public function scopeWithoutUnitScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope(UnitScope::class);
    }

    /**
     * Alias scope untuk agregasi seluruh unit dan portal utama.
     */
    public function scopeAllUnits(Builder $query): Builder
    {
        return $query->withoutGlobalScope(UnitScope::class);
    }

    /**
     * Scope khusus konten Portal Utama.
     */
    public function scopeForPortal(Builder $query): Builder
    {
        return $query->withoutGlobalScope(UnitScope::class)->whereNull($this->getTable() . '.unit_id');
    }

    /**
     * Scope khusus konten Unit tertentu.
     */
    public function scopeForUnit(Builder $query, int $unitId): Builder
    {
        return $query->withoutGlobalScope(UnitScope::class)->where($this->getTable() . '.unit_id', $unitId);
    }
}
