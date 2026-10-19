<?php

namespace App\Policies;

use App\Models\DokumenInformasi;
use App\Models\User;

class DokumenInformasiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage_content') || $user->can('view_ppid') || $user->hasRole('Super Admin') || $user->hasRole('Admin Unit');
    }

    public function view(User $user, DokumenInformasi $doc): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $doc->unit_id === $user->unit_id;
        }

        return $user->can('manage_content') || $user->can('view_ppid');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_content') || $user->hasRole('Super Admin') || $user->hasRole('Admin Unit');
    }

    public function update(User $user, DokumenInformasi $doc): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $doc->unit_id === $user->unit_id;
        }

        return $user->can('manage_content');
    }

    public function delete(User $user, DokumenInformasi $doc): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $doc->unit_id === $user->unit_id;
        }

        return $user->can('manage_content');
    }
}
