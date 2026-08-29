<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberActivityController extends Controller
{
    /**
     * Activity dashboard.
     */
    public function index()
    {
        return view('admin.activities.index');
    }

    /**
     * Search members for activity lookup.
     */
    public function searchMembers(Request $request)
    {
        $search = trim($request->get('search', ''));

        if ($search === '') {
            return response()->json([]);
        }

        $members = SiteMember::query()
            ->where(function ($query) use ($search) {

                $query->where('profile_id', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%");
            })
            ->select([
                'id',
                'profile_id',
                'full_name',
                'email',
                'mobile_number',
                'photo',
            ])
            ->limit(20)
            ->get();

        return response()->json($members);
    }

    /**
     * Show activity for a particular member.
     */
    public function memberActivity(Request $request, $memberId)
    {
        $member = SiteMember::query()
            ->select([
                'id',
                'profile_id',
                'full_name',
                'email',
                'mobile_number',
                'photo',
            ])
            ->findOrFail($memberId);

        $activity = $request->get('activity', 'shortlisted');

        $data = match ($activity) {

            'shortlisted' => $this->shortlisted($member->id),

            'sent-interests' => $this->sentInterests($member->id),

            'received-interests' => $this->receivedInterests($member->id),

            'profile-views' => $this->profileViews($member->id),

            'contact-views' => $this->contactViews($member->id),

            'wallet-payments' => $this->walletPayments($member->id),

            default => abort(404),
        };

        return view('admin.activities.member', [
            'member' => $member,
            'activity' => $activity,
            'data' => $data,
        ]);
    }

    /**
     * Profiles shortlisted BY this member.
     *
     * short_listed.profile_id = members.id
     */
    private function shortlisted($memberId)
    {
        return DB::connection('site')
            ->table('short_listed as sl')

            ->leftJoin(
                'members as m',
                'm.id',
                '=',
                'sl.profile_id'
            )

            ->where('sl.member_id', $memberId)

            ->select([
                'sl.id',
                'sl.profile_id',
                'sl.created_at',

                'm.profile_id as target_profile_id',
                'm.full_name as target_full_name',
            ])

            ->orderByDesc('sl.created_at')

            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Interests sent BY this member.
     *
     * sent_interests.member_id = sender
     * sent_interests.profile_id = recipient
     */
    private function sentInterests($memberId)
    {
        return DB::connection('site')
            ->table('sent_interests as si')

            ->leftJoin(
                'members as m',
                'm.id',
                '=',
                'si.profile_id'
            )

            ->where('si.member_id', $memberId)

            ->select([
                'si.id',
                'si.profile_id',
                'si.status',
                'si.created_at',

                'm.profile_id as target_profile_id',
                'm.full_name as target_full_name',
            ])

            ->orderByDesc('si.created_at')

            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Interests received BY this member.
     *
     * sent_interests.profile_id = recipient
     * sent_interests.member_id = sender
     */
    private function receivedInterests($memberId)
    {
        return DB::connection('site')
            ->table('sent_interests as si')

            ->leftJoin(
                'members as m',
                'm.id',
                '=',
                'si.member_id'
            )

            ->where('si.profile_id', $memberId)

            ->select([
                'si.id',
                'si.member_id',
                'si.status',
                'si.created_at',

                'm.profile_id as sender_profile_id',
                'm.full_name as sender_full_name',
            ])

            ->orderByDesc('si.created_at')

            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Profiles viewed BY this member.
     *
     * profile_viewed.viewed_profile_id = members.id
     */
    private function profileViews($memberId)
    {
        return DB::connection('site')
            ->table('profile_viewed as pv')

            ->leftJoin(
                'members as m',
                'm.id',
                '=',
                'pv.viewed_profile_id'
            )

            ->where('pv.member_id', $memberId)

            ->select([
                'pv.id',
                'pv.viewed_profile_id',
                'pv.created_at',

                'm.profile_id as viewed_profile_id_display',
                'm.full_name as viewed_full_name',
            ])

            ->orderByDesc('pv.created_at')

            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Contacts viewed BY this member.
     *
     * contact_viewed.viewed_profile_id = members.id
     */
    private function contactViews($memberId)
    {
        return DB::connection('site')
            ->table('contact_viewed as cv')

            ->leftJoin(
                'members as m',
                'm.id',
                '=',
                'cv.viewed_profile_id'
            )

            ->where('cv.member_id', $memberId)

            ->select([
                'cv.id',
                'cv.viewed_profile_id',

                'm.profile_id as viewed_profile_id_display',
                'm.full_name as viewed_full_name',
            ])

            ->orderByDesc('cv.id')

            ->paginate(20)

            ->withQueryString();
    }

    /**
     * Wallet payments made BY this member.
     */
    private function walletPayments($memberId)
    {
        return DB::connection('site')
            ->table('member_wallet_payments')

            ->where(
                'member_id',
                (string) $memberId
            )

            ->select([
                'id',
                'member_id',
                'payment_date',
                'amount',
                'payment_id',
                'remarks',
            ])

            ->orderByDesc('payment_date')

            ->paginate(20)

            ->withQueryString();
    }
}
