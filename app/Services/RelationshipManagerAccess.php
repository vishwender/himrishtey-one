<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\SiteMember;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
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

        if (! $admin || $admin->hasRole('super-admin')) {
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

    /**
     * @template TBuilder of EloquentBuilder|QueryBuilder
     *
     * @param  TBuilder  $query
     * @return TBuilder
     */
    public function scope(EloquentBuilder|QueryBuilder $query, string $column = 'relationship_manager')
    {
        if ($this->isRestricted()) {
            $query->whereIn($column, $this->assignedIdentifiers());
        }

        return $query;
    }

    public function canAccessMember(int $memberId): bool
    {
        if (! $this->isRestricted()) {
            return true;
        }

        return $this->scope(SiteMember::withoutGlobalScopes())
            ->whereKey($memberId)
            ->exists();
    }
}
