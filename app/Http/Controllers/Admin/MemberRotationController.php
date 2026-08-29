<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Member;
use App\Models\MemberRotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberRotationController extends Controller
{
    /**
     * Display rotations.
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $canViewAll = $admin->hasPermission('view-all-rotations');

        $canViewOwn = $admin->hasPermission('view-own-rotations');

        /*
        |--------------------------------------------------------------------------
        | Rotations Query
        |--------------------------------------------------------------------------
        */

        $query = MemberRotation::query()
            ->with('member')
            ->orderByRaw('CASE
                WHEN DATE(next_rotation_at) = CURDATE() THEN 1
                WHEN DATE(next_rotation_at) = DATE_ADD(CURDATE(), INTERVAL 1 DAY) THEN 2
                WHEN DATE(next_rotation_at) > DATE_ADD(CURDATE(), INTERVAL 1 DAY) THEN 3
                ELSE 4
            END')
            ->orderBy('next_rotation_at', 'asc');

        /*
        |--------------------------------------------------------------------------
        | Access Control
        |--------------------------------------------------------------------------
        */

        if ($canViewAll) {

            // Can see all rotations.

        } elseif ($canViewOwn) {

            // Can only see rotations assigned to logged-in admin.
            $query->where('user_id', $admin->id);
        } else {

            // No access.
            $query->whereRaw('1 = 0');
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $rotations = $query
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Admins
        |--------------------------------------------------------------------------
        |
        | Admins are stored in the central database.
        |
        */

        $admins = Admin::query()
            ->where('status', true)
            ->orderBy('name')
            ->get()
            ->keyBy('id');

        /*
        |--------------------------------------------------------------------------
        | Summary Query
        |--------------------------------------------------------------------------
        */

        $summaryQuery = MemberRotation::query();

        if ($canViewAll) {

            // All rotations.

        } elseif ($canViewOwn) {

            $summaryQuery->where(
                'user_id',
                $admin->id
            );
        } else {

            $summaryQuery->whereRaw('1 = 0');
        }

        /*
        |--------------------------------------------------------------------------
        | Summary Counts
        |--------------------------------------------------------------------------
        */

        $totalRotations = (clone $summaryQuery)
            ->count();

        $todayRotations = (clone $summaryQuery)
            ->whereDate(
                'next_rotation_at',
                today()
            )
            ->count();

        $tomorrowRotations = (clone $summaryQuery)
            ->whereDate(
                'next_rotation_at',
                now()->addDay()->toDateString()
            )
            ->count();

        $nextTwoDaysRotations = (clone $summaryQuery)
            ->whereBetween(
                'next_rotation_at',
                [
                    now()->startOfDay(),
                    now()->addDays(2)->endOfDay(),
                ]
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Return Listing View
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Do NOT pass $member here.
        |
        */

        return view(
            'admin.member-rotations.index',
            compact(
                'rotations',
                'admins',
                'totalRotations',
                'todayRotations',
                'tomorrowRotations',
                'nextTwoDaysRotations',
                'canViewAll',
                'canViewOwn'
            )
        );
    }

    /**
     * Show create rotation form for a member.
     */
    public function create()
    {

        return view('admin.member-rotations.create');
    }

    /**
     * Store rotation.
     */
    public function store(
        Request $request,
        $memberId
    ) {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        /*
        |--------------------------------------------------------------------------
        | Permission
        |--------------------------------------------------------------------------
        */

        if (! $admin->hasPermission('create-rotations')) {
            abort(403, 'You do not have permission to create rotations.');
        }

        /*
        |--------------------------------------------------------------------------
        | Member
        |--------------------------------------------------------------------------
        */

        $member = Member::findOrFail($memberId);

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
            ],

            'days' => [
                'required',
                'integer',
                'min:1',
                'max:365',
            ],

            'time' => [
                'nullable',
                'date_format:H:i',
            ],

            'next_rotation_at' => [
                'required',
                'date',
            ],

            'status' => [
                'nullable',
                'in:pending,completed,cancelled',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create Rotation
        |--------------------------------------------------------------------------
        */

        MemberRotation::create([
            'member_id' => $member->id,
            'user_id' => $validated['user_id'],
            'days' => $validated['days'],
            'time' => $validated['time'] ?? null,
            'next_rotation_at' => $validated['next_rotation_at'],
            'status' => $validated['status'] ?? 'pending',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.rotations.index')
            ->with(
                'success',
                'Rotation created successfully.'
            );
    }
}
