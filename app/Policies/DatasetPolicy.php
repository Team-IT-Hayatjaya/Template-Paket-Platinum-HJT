<?php

namespace App\Policies;

use App\Models\Dataset;
use App\Models\User;
use App\Support\Package;

class DatasetPolicy
{
    public function viewAny(User $user): bool
    {
        return Package::has('open_data') && ($user->hasRole('Super Admin') || $user->hasRole('Admin Konten') || $user->hasRole('Admin Unit'));
    }

    public function view(User $user, Dataset $dataset): bool
    {
        if (! Package::has('open_data')) {
            return false;
        }

        if ($user->hasRole('Super Admin') || $user->hasRole('Admin Konten')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $dataset->unit_id === $user->unit_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return Package::has('open_data') && ($user->hasRole('Super Admin') || $user->hasRole('Admin Konten') || $user->hasRole('Admin Unit'));
    }

    public function update(User $user, Dataset $dataset): bool
    {
        if (! Package::has('open_data')) {
            return false;
        }

        if ($user->hasRole('Super Admin') || $user->hasRole('Admin Konten')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $dataset->unit_id === $user->unit_id;
        }

        return false;
    }

    public function delete(User $user, Dataset $dataset): bool
    {
        if (! Package::has('open_data')) {
            return false;
        }

        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $dataset->unit_id === $user->unit_id;
        }

        return false;
    }
}
