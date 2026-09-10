<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Member;
use App\Models\MemberRotation;
use App\Services\AdminActivityLogger;
use App\Services\RelationshipManagerAccess;
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

        $canViewOwn = $admin->hasAnyPermission([
            'view-own-rotations',
            'add-rotations',
            'edit-rotations',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Rotations Query
        |--------------------------------------------------------------------------
        */

        $query = MemberRotation::query()
            ->with('member')
            ->orderByRaw('CASE
                WHEN DATE(next_rotation_at) = ? THEN 1
                WHEN DATE(next_rotation_at) = ? THEN 2
                WHEN DATE(next_rotation_at) > ? THEN 3
                ELSE 4
            END', [today()->toDateString(), now()->addDay()->toDateString(), now()->addDay()->toDateString()])
            ->orderBy('next_rotation_at', 'asc');

        if (app(RelationshipManagerAccess::class)->isRestricted()) {
            $query->whereHas('member');
        }

        /*
        |--------------------------------------------------------------------------
        | Access Control
        |--------------------------------------------------------------------------
        */

        if ($canViewAll) {

            // Can see all rotations.

        } elseif ($canViewOwn) {

            // Can only see rotations assigned to logged-in admin.
            $query->where('admin_id', $admin->id);
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

        if (app(RelationshipManagerAccess::class)->isRestricted()) {
            $summaryQuery->whereHas('member');
        }

        if ($canViewAll) {

            // All rotations.

        } elseif ($canViewOwn) {

            $summaryQuery->where(
                'admin_id',
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

        if (! $admin->hasPermission('add-rotations')) {
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
                \Illuminate\Validation\Rule::exists('admins', 'id')->where('status', true),
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
            'admin_id' => $admin->isMemberManager() ? $admin->id : $validated['user_id'],
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

    public function complete(MemberRotation $rotation)
    {
        $this->authorizeRotationOwner($rotation);
        if (! $rotation->member) {
            abort(403, 'You can only complete rotations for members assigned to you.');
        }

        if ($rotation->status === 'cancelled') {
            return back()->with(
                'error',
                'A cancelled rotation cannot be completed.'
            );
        }

        if ($rotation->status === 'completed' || $rotation->completed_at) {
            return back()->with(
                'error',
                'This rotation has already been completed.'
            );
        }

        $rotation->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with(
            'success',
            'Rotation marked as complete successfully.'
        );
    }

    public function destroy(
        MemberRotation $rotation,
        AdminActivityLogger $activityLogger
    ) {
        $this->authorizeRotationOwner($rotation);
        $member = $rotation->member;

        if (app(RelationshipManagerAccess::class)->isRestricted() && ! $member) {
            abort(403, 'You can only delete rotations for members assigned to you.');
        }

        $rotationId = (int) $rotation->id;
        $memberId = (int) $rotation->member_id;
        $rotation->delete();

        $activityLogger->log(
            action: 'rotation_deleted',
            description: $member
                ? "Deleted rotation #{$rotationId} for {$member->profile_id}."
                : "Deleted rotation #{$rotationId}.",
            module: 'members',
            memberId: $memberId,
            subjectType: 'member_rotation',
            subjectId: $rotationId,
            metadata: [
                'profile_id' => $member?->profile_id,
                'full_name' => $member?->full_name,
            ]
        );

        return back()->with(
            'success',
            'Rotation deleted successfully.'
        );
    }

    private function authorizeRotationOwner(MemberRotation $rotation): void
    {
        $admin = Auth::guard('admin')->user();
        if ($admin?->isMemberManager()) {
            abort_unless((int) $rotation->admin_id === (int) $admin->id, 403, 'You can only manage your own rotations.');
        }
    }
}
