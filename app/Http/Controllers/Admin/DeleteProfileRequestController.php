<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\DeleteProfileRequest;
use App\Models\SiteMember;
use App\Services\AdminActivityLogger;
use App\Services\RelationshipManagerAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class DeleteProfileRequestController extends Controller
{
    /**
     * Delete request listing.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $status = $request->input('status', 'pending');

        $perPage = (int) $request->input('per_page', 25);

        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $source = $request->routeIs('admin.members.delete-requests.index') ? 'staff' : 'member';
        $indexRoute = $source === 'staff' ? 'admin.members.delete-requests.index' : 'admin.delete-profile-requests.index';
        $latestIds = DeleteProfileRequest::query()->fromSource($source)
            ->selectRaw('MAX(id)')->groupBy('user_id');
        $summaryQuery = DeleteProfileRequest::query()->fromSource($source)
            ->whereIn('id', $latestIds)
            ->when(app(RelationshipManagerAccess::class)->isRestricted(), fn ($query) => $query->whereHas('member'));

        $query = (clone $summaryQuery)->with('member')->select('delete_profile_request.*');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $query->whereHas('member', function ($memberQuery) use ($search) {

                $memberQuery->where(function ($q) use ($search) {

                    $q->where(
                        'full_name',
                        'like',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'profile_id',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'mobile_number',
                            'like',
                            "%{$search}%"
                        );
                });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status filter
        |--------------------------------------------------------------------------
        */

        if ($status === 'pending') {

            $query->where('status', 0);
        } elseif ($status === 'accepted') {

            $query->where('status', 1);
        } elseif ($status === 'rejected') {

            $query->where('status', 2);
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $requests = $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Load central admins separately
        |--------------------------------------------------------------------------
        |
        | request_by contains the central admin ID.
        | Don't create a cross-database relationship.
        |--------------------------------------------------------------------------
        */

        $counts = DeleteProfileRequest::query()->fromSource($source)
            ->whereIn('user_id', $requests->getCollection()->pluck('user_id'))
            ->selectRaw('user_id, COUNT(*) as request_count')->groupBy('user_id')->pluck('request_count', 'user_id');
        $requests->getCollection()->each(function ($item) use ($counts) {
            $item->request_count = $counts[$item->user_id] ?? 0;
        });

        $adminIds = $requests
            ->getCollection()
            ->pluck('request_by')
            ->filter()
            ->unique()
            ->values();

        $admins = Admin::query()
            ->when($source === 'member', fn ($query) => $query->whereRaw('1 = 0'))
            ->whereIn('id', $adminIds)
            ->get()
            ->keyBy('id');

        $missingAdminIds = $adminIds->reject(fn ($id) => filled($admins->get($id)?->name));

        if ($source === 'staff' && $missingAdminIds->isNotEmpty() && Schema::connection('site')->hasTable('users')) {
            $columns = Schema::connection('site')->getColumnListing('users');
            $nameColumns = array_values(array_intersect(['display_name', 'name'], $columns));

            if ($nameColumns !== []) {
                $legacyStaff = DB::connection('site')->table('users')
                    ->whereIn('id', $missingAdminIds)->get(['id', ...$nameColumns]);

                foreach ($legacyStaff as $staff) {
                    $name = trim((string) ($staff->display_name ?? ''));
                    if ($name === '') {
                        $name = trim((string) ($staff->name ?? ''));
                    }
                    if ($name !== '') {
                        $admins->put($staff->id, (object) ['name' => $name, 'profile_id' => null]);
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        |
        | Count latest request per member.
        |--------------------------------------------------------------------------
        */

        $totalCount = (clone $summaryQuery)->count();

        $pendingCount = (clone $summaryQuery)
            ->where('status', 0)
            ->count();

        $acceptedCount = (clone $summaryQuery)
            ->where('status', 1)
            ->count();

        $rejectedCount = (clone $summaryQuery)
            ->where('status', 2)
            ->count();

        return view(
            'admin.delete-profile-requests.index',
            compact(
                'source',
                'indexRoute',
                'requests',
                'admins',
                'search',
                'status',
                'perPage',
                'totalCount',
                'pendingCount',
                'acceptedCount',
                'rejectedCount'
            )
        );
    }

    public function store(
        Request $request,
        int $member,
        AdminActivityLogger $activityLogger
    ) {
        $admin = auth('admin')->user();

        if (! $admin?->canRaiseProfileDeleteRequest()) {
            abort(403, 'You do not have permission to raise profile delete requests.');
        }

        $validated = $request->validate([
            'reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $siteMember = SiteMember::query()->findOrFail($member);

        $pendingRequestExists = DeleteProfileRequest::query()->fromSource('staff')
            ->where('user_id', $siteMember->id)
            ->where('status', 0)
            ->exists();

        if ($pendingRequestExists) {
            return back()->with(
                'error',
                'A pending delete request already exists for this member.'
            );
        }

        $deleteRequest = DeleteProfileRequest::query()->create([
            'user_id' => $siteMember->id,
            'reason' => $validated['reason'],
            'request_by' => $admin->id,
            'date' => now()->format('d-m-Y'),
            'status' => 0,
        ]);

        $activityLogger->log(
            action: 'profile_delete_requested',
            description: "Raised delete request for {$siteMember->profile_id}.",
            module: 'members',
            memberId: (int) $siteMember->id,
            subjectType: 'delete_profile_request',
            subjectId: (int) $deleteRequest->id,
            metadata: [
                'profile_id' => $siteMember->profile_id,
                'full_name' => $siteMember->full_name,
                'reason' => $validated['reason'],
            ]
        );

        return back()->with(
            'success',
            'Profile delete request raised successfully.'
        );
    }

    /**
     * Accept a single request.
     */
    public function accept(
        int $id,
        AdminActivityLogger $activityLogger
    ) {

        $deleteRequest = DeleteProfileRequest::query()
            ->with('member')
            ->findOrFail($id);

        if ((int) $deleteRequest->status !== 0) {

            return back()->with(
                'error',
                'This delete request has already been processed.'
            );
        }

        $member = $deleteRequest->member;

        $deleteRequest->status = 1;
        $deleteRequest->save();

        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        $activityLogger->log(
            action: 'profile_delete_approved',
            description: $member
                ? "Approved delete request for {$member->profile_id}."
                : "Approved profile delete request #{$deleteRequest->id}.",
            module: 'members',
            memberId: (int) $deleteRequest->user_id,
            subjectType: 'delete_profile_request',
            subjectId: (int) $deleteRequest->id,
            metadata: [
                'profile_id' => $member?->profile_id,
                'full_name' => $member?->full_name,
                'reason' => $deleteRequest->reason,
            ]
        );

        return back()->with(
            'success',
            'Profile delete request accepted successfully.'
        );
    }

    /**
     * Reject a single request.
     */
    public function reject(
        int $id,
        AdminActivityLogger $activityLogger
    ) {

        $deleteRequest = DeleteProfileRequest::query()
            ->with('member')
            ->findOrFail($id);

        if ((int) $deleteRequest->status !== 0) {

            return back()->with(
                'error',
                'This delete request has already been processed.'
            );
        }

        $member = $deleteRequest->member;

        $deleteRequest->status = 2;
        $deleteRequest->save();

        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        $activityLogger->log(
            action: 'profile_delete_rejected',
            description: $member
                ? "Rejected delete request for {$member->profile_id}."
                : "Rejected profile delete request #{$deleteRequest->id}.",
            module: 'members',
            memberId: (int) $deleteRequest->user_id,
            subjectType: 'delete_profile_request',
            subjectId: (int) $deleteRequest->id,
            metadata: [
                'profile_id' => $member?->profile_id,
                'full_name' => $member?->full_name,
                'reason' => $deleteRequest->reason,
            ]
        );

        return back()->with(
            'success',
            'Profile delete request rejected successfully.'
        );
    }

    /**
     * Bulk Accept / Reject.
     */
    public function bulkAction(
        Request $request,
        AdminActivityLogger $activityLogger
    ) {

        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'request_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'request_ids.*' => [
                'required',
                'integer',
            ],

            'action' => [
                'required',
                Rule::in([
                    'accept',
                    'reject',
                ]),
            ],
        ]);

        $requestIds = array_values(
            array_unique(
                array_map(
                    'intval',
                    $validated['request_ids']
                )
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Get ONLY pending requests
        |--------------------------------------------------------------------------
        |
        | This is important.
        |
        | Even if someone manually modifies the HTML and submits an accepted
        | request ID, it will not be processed again.
        |--------------------------------------------------------------------------
        */

        $deleteRequests = DeleteProfileRequest::query()
            ->with('member')
            ->when(
                app(RelationshipManagerAccess::class)->isRestricted(),
                fn ($query) => $query->whereHas('member')
            )
            ->whereIn('id', $requestIds)
            ->where('status', 0)
            ->get();

        if ($deleteRequests->isEmpty()) {

            return back()->with(
                'error',
                'No pending delete requests were selected.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Determine new status
        |--------------------------------------------------------------------------
        */

        $newStatus = $validated['action'] === 'accept'
            ? 1
            : 2;

        $processedCount = 0;

        /*
        |--------------------------------------------------------------------------
        | Site DB transaction
        |--------------------------------------------------------------------------
        */

        DB::connection('site')->transaction(
            function () use (
                $deleteRequests,
                $newStatus,
                &$processedCount
            ) {

                foreach ($deleteRequests as $deleteRequest) {

                    /*
                    |--------------------------------------------------------------------------
                    | Re-check status inside transaction
                    |--------------------------------------------------------------------------
                    */

                    $updated = DB::connection('site')
                        ->table('delete_profile_request')
                        ->where('id', $deleteRequest->id)
                        ->where('status', 0)
                        ->update([
                            'status' => $newStatus,
                        ]);

                    if ($updated === 1) {

                        $processedCount++;
                    }
                }
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Audit each member
        |--------------------------------------------------------------------------
        |
        | Audit DB is central, so intentionally keep it outside the site DB
        | transaction.
        |--------------------------------------------------------------------------
        */

        foreach ($deleteRequests as $deleteRequest) {

            $member = $deleteRequest->member;

            if ($validated['action'] === 'accept') {

                $activityLogger->log(
                    action: 'profile_delete_approved',
                    description: $member
                        ? "Bulk approved delete request for {$member->profile_id}."
                        : "Bulk approved profile delete request #{$deleteRequest->id}.",
                    module: 'members',
                    memberId: (int) $deleteRequest->user_id,
                    subjectType: 'delete_profile_request',
                    subjectId: (int) $deleteRequest->id,
                    metadata: [
                        'profile_id' => $member?->profile_id,
                        'full_name' => $member?->full_name,
                        'reason' => $deleteRequest->reason,
                        'bulk_action' => true,
                    ]
                );
            } else {

                $activityLogger->log(
                    action: 'profile_delete_rejected',
                    description: $member
                        ? "Bulk rejected delete request for {$member->profile_id}."
                        : "Bulk rejected profile delete request #{$deleteRequest->id}.",
                    module: 'members',
                    memberId: (int) $deleteRequest->user_id,
                    subjectType: 'delete_profile_request',
                    subjectId: (int) $deleteRequest->id,
                    metadata: [
                        'profile_id' => $member?->profile_id,
                        'full_name' => $member?->full_name,
                        'reason' => $deleteRequest->reason,
                        'bulk_action' => true,
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        $actionText = $validated['action'] === 'accept'
            ? 'accepted'
            : 'rejected';

        return back()->with(
            'success',
            "{$processedCount} profile delete request(s) {$actionText} successfully."
        );
    }
}
