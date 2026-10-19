<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;

class BannerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage_content') || $user->hasRole('Super Admin') || $user->hasRole('Admin Unit');
    }

    public function view(User $user, Banner $banner): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $banner->unit_id === $user->unit_id;
        }

        return $user->can('manage_content');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_content') || $user->hasRole('Super Admin') || $user->hasRole('Admin Unit');
    }

    public function update(User $user, Banner $banner): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $banner->unit_id === $user->unit_id;
        }

        return $user->can('manage_content');
    }

    public function delete(User $user, Banner $banner): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $banner->unit_id === $user->unit_id;
        }

        return $user->can('manage_content');
    }
}
