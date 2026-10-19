<?php

namespace App\Policies;

use App\Models\SiteSetting;
use App\Models\User;

class SiteSettingPolicy
{
    /**
     * Determine whether the user can view the site settings.
     */
    public function view(User $user, SiteSetting $siteSetting): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin Konten']);
    }

    /**
     * Determine whether the user can update the site settings.
     */
    public function update(User $user, SiteSetting $siteSetting): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin Konten']);
    }
}
