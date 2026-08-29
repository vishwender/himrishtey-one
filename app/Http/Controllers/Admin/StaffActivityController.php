<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminActivityLog;
use App\Models\Site;
use Illuminate\Http\Request;

class StaffActivityController extends Controller
{
    /**
     * Display staff activity logs.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Activity Query
        |--------------------------------------------------------------------------
        */

        $query = AdminActivityLog::query()
            ->with([
                'admin:id,name,email',
                'site:id,name,code',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        |
        | Searches activity description and member ID.
        |
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%");

                if (is_numeric($search)) {
                    $q->orWhere('member_id', (int) $search);
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Staff Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('admin_id')) {
            $query->where(
                'admin_id',
                (int) $request->admin_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Site Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('site_id')) {
            $query->where(
                'site_id',
                (int) $request->site_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Activity Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('action')) {
            $query->where(
                'action',
                $request->action
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date From
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date To
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Results
        |--------------------------------------------------------------------------
        */

        $activities = $query
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Staff
        |--------------------------------------------------------------------------
        */

        $admins = Admin::query()
            ->select([
                'id',
                'name',
                'email',
            ])
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Sites
        |--------------------------------------------------------------------------
        */

        $sites = Site::query()
            ->select([
                'id',
                'name',
                'code',
            ])
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Available Activity Types
        |--------------------------------------------------------------------------
        */

        $actions = AdminActivityLog::query()
            ->whereNotNull('action')
            ->where('action', '!=', '')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view(
            'admin.staff-activity.index',
            compact(
                'activities',
                'admins',
                'sites',
                'actions'
            )
        );
    }

    public function show(Request $request, $adminId)
    {
        $admin = Admin::query()
            ->findOrFail($adminId);

        $query = AdminActivityLog::query()
            ->with('site:id,name,code')
            ->where('admin_id', $admin->id);

        /*
    |--------------------------------------------------------------------------
    | Site Filter
    |--------------------------------------------------------------------------
    */

        if ($request->filled('site_id')) {

            $query->where(
                'site_id',
                (int) $request->site_id
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Activity Filter
    |--------------------------------------------------------------------------
    */

        if ($request->filled('action')) {

            $query->where(
                'action',
                $request->action
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Date From
    |--------------------------------------------------------------------------
    */

        if ($request->filled('date_from')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Date To
    |--------------------------------------------------------------------------
    */

        if ($request->filled('date_to')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Activities
    |--------------------------------------------------------------------------
    */

        $activities = $query
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        /*
    |--------------------------------------------------------------------------
    | Sites
    |--------------------------------------------------------------------------
    */

        $sites = Site::query()
            ->select([
                'id',
                'name',
                'code',
            ])
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Activity Types For This Staff Member
    |--------------------------------------------------------------------------
    */

        $actions = AdminActivityLog::query()
            ->where('admin_id', $admin->id)
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view(
            'admin.staff-users.activity',
            compact(
                'admin',
                'activities',
                'sites',
                'actions'
            )
        );
    }
}
