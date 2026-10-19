<?php

namespace App\Policies;

use App\Models\PermohonanInformasi;
use App\Models\User;

class PermohonanInformasiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->can('view_ppid');
    }

    public function view(User $user, PermohonanInformasi $permohonan): bool
    {
        return $user->hasRole('Super Admin') || $user->can('view_ppid');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->can('process_ppid');
    }

    public function update(User $user, PermohonanInformasi $permohonan): bool
    {
        return $user->hasRole('Super Admin') || $user->can('process_ppid');
    }

    public function delete(User $user, PermohonanInformasi $permohonan): bool
    {
        return $user->hasRole('Super Admin');
    }
}
