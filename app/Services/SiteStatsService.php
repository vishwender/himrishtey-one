<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SiteStatsService
{
    private string $connection = 'site';

    public function statistics(int $months): array
    {
        return [
            'core' => $this->coreMemberStats(),
            'site_hits' => $this->siteHits(),
            'gender' => $this->groupMembers('gender'),
            'demographics' => [
                'marital_status' => $this->groupMembers('marital_status', 8),
                'religion' => $this->groupMembers('religion', 8),
            ],
            'quality' => $this->profileQuality(),
            'registrations' => $this->registrationTrend($months),
            'activity' => $this->activityStats($months),
            'membership' => $this->membershipStats(),
            'finance' => $this->financeStats($months),
            'workflow' => $this->workflowStats($months),
            'locations' => [
                'countries' => $this->groupMembers('country_living_in', 10),
                'states' => $this->groupMembers('state_living_in', 10),
                'cities' => $this->groupMembers('city_living_in', 10),
            ],
        ];
    }

    private function coreMemberStats(): array
    {
        if (! $this->tableExists('members')) {
            return array_fill_keys(['total', 'active', 'inactive', 'trusted', 'promoted', 'hidden'], 0);
        }

        $query = DB::connection($this->connection)->table('members');

        return [
            'total' => (clone $query)->count(),
            'active' => $this->countValue($query, 'active', 'yes'),
            'inactive' => $this->countNotValue($query, 'active', 'yes'),
            'trusted' => $this->countValue($query, 'is_trusted', 'yes'),
            'promoted' => $this->countValue($query, 'promoted', 'yes'),
            'hidden' => $this->countValue($query, 'profile_hide', 'yes'),
        ];
    }

    private function siteHits(): int
    {
        if (! $this->columnExists('settings', 'hits')) {
            return 0;
        }

        return (int) DB::connection($this->connection)
            ->table('settings')
            ->sum('hits');
    }

    private function profileQuality(): array
    {
        if (! $this->tableExists('members')) {
            return array_fill_keys(['with_photo', 'approved_photo', 'trusted', 'complete', 'needs_attention'], 0);
        }

        $base = DB::connection($this->connection)->table('members');
        $total = (clone $base)->count();
        $withPhoto = $this->countPresent($base, 'photo');
        $approved = $this->countValue($base, 'photo_approved', 'yes');
        $trusted = $this->countValue($base, 'is_trusted', 'yes');
        $complete = $this->columnExists('members', 'profile_completed')
            ? (clone $base)->whereRaw('CAST(profile_completed AS DECIMAL(10,2)) >= 80')->count()
            : 0;

        return [
            'with_photo' => $withPhoto,
            'approved_photo' => $approved,
            'trusted' => $trusted,
            'complete' => $complete,
            'needs_attention' => max(0, $total - $complete),
        ];
    }

    private function registrationTrend(int $months): array
    {
        $labels = collect(range($months - 1, 0))->map(fn ($offset) => now()->subMonths($offset)->format('M Y'))->push(now()->format('M Y'));
        $counts = array_fill_keys($labels->all(), 0);

        if ($this->columnExists('members', 'registration_date')) {
            DB::connection($this->connection)->table('members')
                ->select('registration_date')->orderByDesc('id')->pluck('registration_date')
                ->each(function ($value) use (&$counts) {
                    try {
                        $key = Carbon::parse($value)->format('M Y');
                        if (array_key_exists($key, $counts)) {
                            $counts[$key]++;
                        }
                    } catch (\Throwable) {
                        // Ignore legacy dates that cannot be parsed.
                    }
                });
        }

        return ['labels' => array_keys($counts), 'values' => array_values($counts)];
    }

    private function activityStats(int $months): array
    {
        return [
            'sent_interests' => $this->datedTableCount('sent_interests', 'created_at', $months),
            'profile_views' => $this->datedTableCount('profile_viewed', 'created_at', $months),
            'contact_views' => $this->datedTableCount('contact_viewed', 'created_at', $months),
            'activity_events' => $this->datedTableCount('user_activity_logs', 'created_at', $months),
        ];
    }

    private function membershipStats(): array
    {
        if (! $this->tableExists('members')) {
            return ['paid' => 0, 'free' => 0, 'plans' => []];
        }

        $members = DB::connection($this->connection)->table('members');
        $paid = $this->columnExists('members', 'plan_id')
            ? (clone $members)->whereNotNull('plan_id')->whereNotIn('plan_id', ['', '0'])->count()
            : 0;

        $plans = [];
        if ($this->tableExists('membership_plans') && $this->columnExists('members', 'plan_id')) {
            $plans = DB::connection($this->connection)->table('members as m')
                ->leftJoin('membership_plans as p', 'p.id', '=', 'm.plan_id')
                ->whereNotIn('m.plan_id', ['', '0'])
                ->selectRaw("COALESCE(NULLIF(p.plan_name, ''), CONCAT('Plan ', m.plan_id)) AS label, COUNT(*) AS total")
                ->groupBy('label')->orderByDesc('total')->limit(8)->pluck('total', 'label')->map(fn ($value) => (int) $value)->all();
        }

        return ['paid' => $paid, 'free' => max(0, (clone $members)->count() - $paid), 'plans' => $plans];
    }

    private function financeStats(int $months): array
    {
        $payments = $this->tableExists('payments') ? DB::connection($this->connection)->table('payments') : null;
        $paymentCount = $payments ? $this->applyDateFilter(clone $payments, 'payments', 'payment_date', $months)->count() : 0;
        $revenue = $payments && $this->columnExists('payments', 'amount')
            ? (float) $this->applyDateFilter(clone $payments, 'payments', 'payment_date', $months)->sum('amount') : 0;

        $walletBalance = $this->tableExists('member_wallet') && $this->columnExists('member_wallet', 'wallet_balance')
            ? (float) DB::connection($this->connection)->table('member_wallet')->sum(DB::raw('CAST(wallet_balance AS DECIMAL(12,2))')) : 0;
        $walletAdded = $this->tableExists('member_wallet') && $this->columnExists('member_wallet', 'amount_added')
            ? (float) DB::connection($this->connection)->table('member_wallet')->sum(DB::raw('CAST(amount_added AS DECIMAL(12,2))')) : 0;

        return compact('paymentCount', 'revenue', 'walletBalance', 'walletAdded');
    }

    private function workflowStats(int $months): array
    {
        $rotations = ['pending' => 0, 'completed' => 0, 'cancelled' => 0, 'overdue' => 0];
        if ($this->tableExists('member_rotations')) {
            $base = DB::connection($this->connection)->table('member_rotations');
            foreach (['pending', 'completed', 'cancelled'] as $status) {
                $rotations[$status] = (clone $base)->where('status', $status)->count();
            }
            if ($this->columnExists('member_rotations', 'next_rotation_at')) {
                $rotations['overdue'] = (clone $base)->where('next_rotation_at', '<', now())
                    ->where(fn ($q) => $q->whereNull('status')->orWhereNotIn('status', ['completed', 'cancelled']))->count();
            }
        }

        $siteId = session('admin_site_id');
        $staffActions = $siteId
            ? AdminActivityLog::where('site_id', $siteId)->where('created_at', '>=', now()->subMonths($months))->count()
            : 0;

        return ['rotations' => $rotations, 'staff_actions' => $staffActions];
    }

    private function groupMembers(string $column, int $limit = 10): array
    {
        if (! $this->columnExists('members', $column)) {
            return [];
        }

        return DB::connection($this->connection)->table('members')
            ->whereNotNull($column)->where($column, '!=', '')
            ->selectRaw("TRIM({$column}) AS label, COUNT(*) AS total")
            ->groupBy('label')->orderByDesc('total')->limit($limit)
            ->pluck('total', 'label')->map(fn ($value) => (int) $value)->all();
    }

    private function datedTableCount(string $table, string $column, int $months): int
    {
        if (! $this->tableExists($table)) {
            return 0;
        }

        return $this->applyDateFilter(DB::connection($this->connection)->table($table), $table, $column, $months)->count();
    }

    private function applyDateFilter($query, string $table, string $column, int $months)
    {
        return $this->columnExists($table, $column) ? $query->where($column, '>=', now()->subMonths($months)) : $query;
    }

    private function countValue($query, string $column, string $value): int
    {
        return $this->columnExists('members', $column)
            ? (clone $query)->whereRaw("LOWER(TRIM({$column})) = ?", [$value])->count() : 0;
    }

    private function countNotValue($query, string $column, string $value): int
    {
        return $this->columnExists('members', $column)
            ? (clone $query)->where(fn ($q) => $q->whereNull($column)->orWhereRaw("LOWER(TRIM({$column})) != ?", [$value]))->count() : 0;
    }

    private function countPresent($query, string $column): int
    {
        return $this->columnExists('members', $column)
            ? (clone $query)->whereNotNull($column)->where($column, '!=', '')->count() : 0;
    }

    private function tableExists(string $table): bool
    {
        return Schema::connection($this->connection)->hasTable($table);
    }

    private function columnExists(string $table, string $column): bool
    {
        return $this->tableExists($table) && Schema::connection($this->connection)->hasColumn($table, $column);
    }
}
