<?php

namespace App\Policies;

use App\Models\NavigationMenu;
use App\Models\User;

class NavigationMenuPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage_content') || $user->hasRole('Super Admin') || $user->hasRole('Admin Unit');
    }

    public function view(User $user, NavigationMenu $menu): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $menu->unit_id === $user->unit_id;
        }

        return $user->can('manage_content');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_content') || $user->hasRole('Super Admin') || $user->hasRole('Admin Unit');
    }

    public function update(User $user, NavigationMenu $menu): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $menu->unit_id === $user->unit_id;
        }

        return $user->can('manage_content');
    }

    public function delete(User $user, NavigationMenu $menu): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin Unit')) {
            return $user->unit_id !== null && $menu->unit_id === $user->unit_id;
        }

        return $user->can('manage_content');
    }
}
