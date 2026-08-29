<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\DeleteProfileRequest;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        /*
        |--------------------------------------------------------------------------
        | Latest request for each member
        |--------------------------------------------------------------------------
        */

        $query = DeleteProfileRequest::query()
            ->with('member')
            ->whereIn('id', function ($subQuery) {

                $subQuery
                    ->selectRaw('MAX(id)')
                    ->from('delete_profile_request')
                    ->groupBy('user_id');
            })
            ->select('delete_profile_request.*')

            /*
            |--------------------------------------------------------------------------
            | Number of requests raised for this member
            |--------------------------------------------------------------------------
            */

            ->selectSub(function ($subQuery) {

                $subQuery
                    ->from('delete_profile_request as dpr_count')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn(
                        'dpr_count.user_id',
                        'delete_profile_request.user_id'
                    );
            }, 'request_count');

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

        $adminIds = $requests
            ->getCollection()
            ->pluck('request_by')
            ->filter()
            ->unique()
            ->values();

        $admins = Admin::query()
            ->whereIn('id', $adminIds)
            ->get()
            ->keyBy('id');

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        |
        | Count latest request per member.
        |--------------------------------------------------------------------------
        */

        $latestIds = DB::connection('site')
            ->table('delete_profile_request')
            ->selectRaw('MAX(id) as id')
            ->groupBy('user_id');

        $summaryQuery = DB::connection('site')
            ->table('delete_profile_request')
            ->whereIn('id', $latestIds);

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
