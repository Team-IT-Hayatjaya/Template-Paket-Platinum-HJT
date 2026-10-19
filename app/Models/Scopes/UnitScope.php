<?php

namespace App\Models\Scopes;

use App\Facades\UnitContext;
use App\Support\Package;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UnitScope implements Scope
{
    /**
     * Terapkan isolasi data berdasarkan unit UPTD/Bidang aktif.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // 1. Isolasi hanya aktif bila paket saat ini mendukung subdomains (Platinum)
        if (! Package::has('subdomains')) {
            return;
        }

        $table = $model->getTable();

        // 2. Jika diakses di Panel Admin Filament
        if (request()->is('admin*') && auth()->check()) {
            $user = auth()->user();

            // Admin Unit hanya boleh melihat data milik unitnya sendiri
            if ($user->hasRole('Admin Unit') && $user->unit_id) {
                $builder->where("{$table}.unit_id", $user->unit_id);
                return;
            }

            // Super Admin dapat melihat semua data (tanpa isolasi otomatis)
            if ($user->hasRole('Super Admin')) {
                return;
            }
        }

        // 3. Jika sedang dalam konteks subdomain Unit tertentu (misal: dispora.pemprov.go.id)
        if (UnitContext::hasUnit()) {
            $builder->where("{$table}.unit_id", UnitContext::unitId());
            return;
        }

        // 4. Jika sedang di Portal Utama
        // Hanya tampilkan konten induk Portal Utama (unit_id IS NULL)
        $builder->whereNull("{$table}.unit_id");
    }
}
