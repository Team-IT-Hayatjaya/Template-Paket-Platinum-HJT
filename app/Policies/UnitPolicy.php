<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use App\Support\Package;

class UnitPolicy
{
    public function viewAny(User $user): bool
    {
        return Package::has('subdomains') && $user->hasRole('Super Admin');
    }

    public function view(User $user, Unit $unit): bool
    {
        return Package::has('subdomains') && ($user->hasRole('Super Admin') || ($user->hasRole('Admin Unit') && $user->unit_id === $unit->id));
    }

    public function create(User $user): bool
    {
        if (! Package::has('subdomains') || ! $user->hasRole('Super Admin')) {
            return false;
        }

        $limit = Package::limit('subdomains') ?? 5;
        return Unit::count() < $limit;
    }

    public function update(User $user, Unit $unit): bool
    {
        return Package::has('subdomains') && $user->hasRole('Super Admin');
    }

    public function delete(User $user, Unit $unit): bool
    {
        return Package::has('subdomains') && $user->hasRole('Super Admin');
    }
}
