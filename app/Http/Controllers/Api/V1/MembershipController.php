<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\Api\V1\ApplicationDatabaseService;
use App\Services\NimbusSmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

class MembershipController extends Controller
{
    private const int CALLBACK_COOLDOWN_MINUTES = 10;

    public function __construct(
        private ApplicationDatabaseService $databaseService
    ) {}

    /**
     * Get all membership types.
     */
    public function index(): JsonResponse
    {
        $connection = $this->databaseService->connection();

        $memberships = $connection
            ->table('membership_type')
            ->select([
                'id',
                'plan_name',
                'plan_guide',
                'plan_description',
                'terms_and_conditions',
            ])
            ->orderBy('id')
            ->get()
            ->map(function ($membership) {

                return [
                    'id' => (int) $membership->id,

                    'name' => $membership->plan_name,

                    'plan_guide' => $membership->plan_guide,

                    'plan_description' => $membership->plan_description,

                    'terms_and_conditions' => $membership->terms_and_conditions,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Memberships fetched successfully.',
            'data' => $memberships,
        ]);
    }

    /**
     * Get plans belonging to a membership type.
     */
    public function plans(int $membershipTypeId): JsonResponse
    {
        $connection = $this->databaseService->connection();

        /*
        |--------------------------------------------------------------------------
        | Membership type
        |--------------------------------------------------------------------------
        */

        $membership = $connection
            ->table('membership_type')
            ->where('id', $membershipTypeId)
            ->first();

        if (! $membership) {

            return response()->json([
                'success' => false,
                'message' => 'Membership not found.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Plans
        |--------------------------------------------------------------------------
        */

        $plans = $connection
            ->table('membership_plans')
            ->where(
                'membership_type',
                $membershipTypeId
            )
            ->orderBy('duration_days')
            ->get([
                'id',
                'membership_type',
                'plan_name',
                'duration_days',
                'view_contact',
                'view_profile',
                'plan_cost',
                'discount_percentage',
                'final_cost',
            ])
            ->map(function ($plan) {

                return [
                    'id' => (int) $plan->id,

                    'plan_name' => $plan->plan_name,

                    'duration_days' => (int) $plan->duration_days,

                    'view_contact' => (int) $plan->view_contact,

                    'view_profile' => (int) $plan->view_profile,

                    'plan_cost' => (int) $plan->plan_cost,

                    'discount_percentage' => (int) $plan->discount_percentage,

                    'final_cost' => $plan->final_cost,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Membership plans fetched successfully.',
            'data' => [
                'membership' => [
                    'id' => (int) $membership->id,

                    'name' => $membership->plan_name,

                    'plan_guide' => $membership->plan_guide,

                    'plan_description' => $membership->plan_description,

                    'terms_and_conditions' => $membership->terms_and_conditions,
                ],

                'plans' => $plans,
            ],
        ]);
    }

    public function requestCallback(
        Request $request,
        NimbusSmsService $smsService
    ): JsonResponse {
        /** @var Member|null $member */
        $member = $request->user();

        if (! $member) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $application = $request->attributes->get('application');
        $cacheKey = "api:callback-request:{$application->id}:{$member->id}";
        $cooldownExpiresAt = Cache::get($cacheKey);

        if (is_int($cooldownExpiresAt) && $cooldownExpiresAt > now()->timestamp) {
            return response()->json([
                'success' => false,
                'message' => 'A callback request was already sent recently.',
                'retry_after' => $cooldownExpiresAt - now()->timestamp,
            ], 429);
        }

        $recipient = (string) config('services.nimbus.callback_recipient');

        if (blank($recipient)) {
            return response()->json([
                'success' => false,
                'message' => 'Callback service is not configured.',
            ], 503);
        }

        try {
            $sent = $smsService->sendCallbackRequest(
                $recipient,
                (string) $member->profile_id,
                (string) $member->full_name
            );
        } catch (Throwable $exception) {
            report($exception);
            $sent = false;
        }

        if (! $sent) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to send the callback request. Please try again.',
            ], 503);
        }

        $cooldownExpiresAt = now()->addMinutes(self::CALLBACK_COOLDOWN_MINUTES);
        Cache::put($cacheKey, $cooldownExpiresAt->timestamp, $cooldownExpiresAt);

        return response()->json([
            'success' => true,
            'message' => 'Your callback request has been sent successfully.',
            'data' => [
                'cooldown_seconds' => self::CALLBACK_COOLDOWN_MINUTES * 60,
            ],
        ]);
    }
}
