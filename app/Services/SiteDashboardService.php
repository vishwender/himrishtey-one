<?php

namespace App\Services;

use App\Models\SiteMember;
use Illuminate\Support\Facades\DB;

class SiteDashboardService
{
    /**
     * Get dashboard statistics for the currently selected site.
     */
    public function statistics(): array
    {
        return [
            'members' => $this->members(),

            'active_members' => $this->activeMembers(),

            'inactive_members' => $this->inactiveMembers(),

            'payments' => $this->payments(),
        ];
    }

    /**
     * Total members.
     */
    protected function members(): int
    {
        return SiteMember::count();
    }

    /**
     * Active members.
     *
     * We will verify the actual values of the active column
     * before finalizing this condition.
     */
    protected function activeMembers(): int
    {
        return SiteMember::where('active', 'Yes')->count();
    }

    /**
     * Pending profiles.
     *
     * Temporary definition:
     * profiles that are not active yet.
     *
     * We will verify your actual application logic before
     * considering this final.
     */
    protected function inactiveMembers(): int
    {
        return SiteMember::where(function ($query) {
            $query->where('active', 'No')
                ->orWhereNull('active');
        })->count();
    }

    /**
     * Total payments.
     *
     * We will refine this after checking the payments table.
     */
    protected function payments(): int
    {
        if (app(RelationshipManagerAccess::class)->isRestricted()) {
            return 0;
        }

        return DB::connection('site')
            ->table('payments')
            ->count();
    }
}
