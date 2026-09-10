<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberRotation;
use App\Services\RelationshipManagerAccess;
use App\Services\SiteDashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(SiteDashboardService $dashboardService)
    {
        $admin = Auth::guard('admin')->user();
        $rotationsOnly = $admin?->isMemberManager() ?? false;
        $stats = $rotationsOnly ? [] : $dashboardService->statistics();

        $rotationNotifications = collect();

        $rotationTodayCount = 0;
        $rotationTomorrowCount = 0;
        $rotationDayAfterTomorrowCount = 0;

        if ($admin) {

            $canViewAll = $admin->hasPermission('view-all-rotations');
            $canViewOwn = $admin->hasPermission('view-own-rotations');

            /*
    |--------------------------------------------------------------------------
    | Rotation Notification Query
    |--------------------------------------------------------------------------
    */

            $rotationQuery = MemberRotation::query()
                ->with('member')
                ->whereBetween('next_rotation_at', [
                    now()->startOfDay(),
                    now()->addDays(2)->endOfDay(),
                ])
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhereNotIn('status', [
                            'completed',
                            'cancelled',
                        ]);
                });

            if (app(RelationshipManagerAccess::class)->isRestricted()) {
                $rotationQuery->whereHas('member');
            }

            /*
    |--------------------------------------------------------------------------
    | Permission Filter
    |--------------------------------------------------------------------------
    */

            if ($canViewAll) {

                // Show all rotations.

            } elseif ($canViewOwn) {

                $rotationQuery->where(
                    'admin_id',
                    $admin->id
                );
            } else {

                $rotationQuery->whereRaw('1 = 0');
            }

            /*
    |--------------------------------------------------------------------------
    | Get Rotations
    |--------------------------------------------------------------------------
    */

            $rotationNotifications = $rotationQuery
                ->orderBy('next_rotation_at', 'asc')
                ->get();

            /*
    |--------------------------------------------------------------------------
    | Counts
    |--------------------------------------------------------------------------
    */

            $rotationTodayCount = $rotationNotifications
                ->filter(function ($rotation) {

                    return $rotation->next_rotation_at
                        && $rotation->next_rotation_at->isToday();
                })
                ->count();

            $rotationTomorrowCount = $rotationNotifications
                ->filter(function ($rotation) {

                    return $rotation->next_rotation_at
                        && $rotation->next_rotation_at
                            ->isSameDay(now()->addDay());
                })
                ->count();

            $rotationDayAfterTomorrowCount = $rotationNotifications
                ->filter(function ($rotation) {

                    return $rotation->next_rotation_at
                        && $rotation->next_rotation_at
                            ->isSameDay(now()->addDays(2));
                })
                ->count();
        }

        return view('admin.dashboard', compact(
            'stats',
            'rotationsOnly',
            'rotationNotifications',
            'rotationTodayCount',
            'rotationTomorrowCount',
            'rotationDayAfterTomorrowCount'
        ));
    }
}
