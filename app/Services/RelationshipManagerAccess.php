<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\SiteMember;
use Illuminate\Support\Facades\Auth;

class RelationshipManagerAccess
{
    public function admin(): ?Admin
    {
        $admin = Auth::guard('admin')->user();

        return $admin instanceof Admin ? $admin : null;
    }

    public function isRestricted(): bool
    {
        $admin = $this->admin();

        if (! $admin || $admin->hasRole('super-admin') || $admin->isMemberManager()) {
            return false;
        }

        return $admin->hasRole('relationship-manager');
    }

    /** @return list<string> */
    public function assignedIdentifiers(): array
    {
        $admin = $this->admin();

        if (! $admin) {
            return [];
        }

        return array_values(array_unique(array_filter([
            trim((string) $admin->name),
            trim((string) $admin->profile_id),
        ])));
    }

    public function canAccessMember(int $memberId): bool
    {
        if (! $this->isRestricted()) {
            return true;
        }

        return SiteMember::query()
            ->whereKey($memberId)
            ->exists();
    }
}
