<?php

namespace App\Policies;

use App\Models\IpBlocklist;
use App\Models\User;
use App\Support\Package;

/**
 * Hanya Super Admin pada paket dengan fitur security_hardening yang boleh
 * mengelola daftar blokir IP.
 */
class IpBlocklistPolicy
{
    protected function allowed(User $user): bool
    {
        return Package::has('security_hardening') && $user->hasRole('Super Admin');
    }

    public function viewAny(User $user): bool
    {
        return $this->allowed($user);
    }

    public function view(User $user, IpBlocklist $ipBlocklist): bool
    {
        return $this->allowed($user);
    }

    public function create(User $user): bool
    {
        return $this->allowed($user);
    }

    public function update(User $user, IpBlocklist $ipBlocklist): bool
    {
        return $this->allowed($user);
    }

    public function delete(User $user, IpBlocklist $ipBlocklist): bool
    {
        return $this->allowed($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allowed($user);
    }
}
