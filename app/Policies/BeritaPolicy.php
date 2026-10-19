<?php

namespace App\Policies;

use App\Models\Berita;
use App\Models\User;

class BeritaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage_content') || $user->hasRole('Super Admin') || $user->hasRole('Admin Unit');
    }

    public function view(User $user, Berita $berita): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $berita->unit_id === $user->unit_id;
        }

        return $user->can('manage_content');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_content') || $user->hasRole('Super Admin') || $user->hasRole('Admin Unit');
    }

    public function update(User $user, Berita $berita): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $berita->unit_id === $user->unit_id;
        }

        return $user->can('manage_content');
    }

    public function delete(User $user, Berita $berita): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $berita->unit_id === $user->unit_id;
        }

        return $user->can('manage_content');
    }
}
