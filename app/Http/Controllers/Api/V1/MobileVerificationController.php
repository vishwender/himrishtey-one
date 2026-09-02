<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\VerifyMobileOtpRequest;
use App\Models\Member;
use App\Services\NimbusSmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class MobileVerificationController extends Controller
{
    private const int OTP_EXPIRY_MINUTES = 5;

    private const int OTP_MAX_ATTEMPTS = 5;

    public function requestOtp(Request $request, NimbusSmsService $smsService): JsonResponse
    {
        /** @var Member|null $member */
        $member = $request->user();

        if (! $member) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (strtolower(trim((string) $member->member_type)) === 'verified') {
            return response()->json([
                'success' => false,
                'message' => 'This mobile number is already verified.',
            ], 409);
        }

        if (blank($member->mobile_number)) {
            return response()->json([
                'success' => false,
                'message' => 'No mobile number is registered with this account.',
            ], 422);
        }

        $application = $request->attributes->get('application');
        $otp = (string) random_int(1000, 9999);

        try {
            $sent = $smsService->sendMobileVerificationOtp(
                (string) $member->mobile_number,
                $otp
            );
        } catch (Throwable $exception) {
            report($exception);
            $sent = false;
        }

        if (! $sent) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to send the verification OTP. Please try again.',
            ], 503);
        }

        $challengeId = Str::random(64);
        $expiresAt = now()->addMinutes(self::OTP_EXPIRY_MINUTES);

        Cache::put($this->cacheKey($challengeId), [
            'application_id' => (string) $application->id,
            'member_id' => $member->id,
            'mobile_number' => (string) $member->mobile_number,
            'otp_hash' => Hash::make($otp),
            'attempts_remaining' => self::OTP_MAX_ATTEMPTS,
            'expires_at' => $expiresAt->timestamp,
        ], $expiresAt);

        return response()->json([
            'success' => true,
            'message' => 'Verification OTP sent successfully.',
            'data' => [
                'challenge_id' => $challengeId,
                'expires_in' => self::OTP_EXPIRY_MINUTES * 60,
                'mobile_number' => $this->maskMobileNumber((string) $member->mobile_number),
            ],
        ]);
    }

    public function verifyOtp(VerifyMobileOtpRequest $request): JsonResponse
    {
        /** @var Member|null $member */
        $member = $request->user();
        $challengeId = $request->string('challenge_id')->toString();
        $cacheKey = $this->cacheKey($challengeId);
        $challenge = Cache::pull($cacheKey);
        $application = $request->attributes->get('application');

        if (
            ! $member
            || ! is_array($challenge)
            || ($challenge['expires_at'] ?? 0) < now()->timestamp
            || ! hash_equals((string) $challenge['application_id'], (string) $application->id)
            || (int) $challenge['member_id'] !== (int) $member->id
            || ! hash_equals((string) $challenge['mobile_number'], (string) $member->mobile_number)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'The verification challenge is invalid or has expired.',
            ], 422);
        }

        if (! Hash::check($request->string('otp')->toString(), $challenge['otp_hash'])) {
            $attemptsRemaining = (int) $challenge['attempts_remaining'] - 1;

            if ($attemptsRemaining > 0) {
                $challenge['attempts_remaining'] = $attemptsRemaining;
                Cache::put(
                    $cacheKey,
                    $challenge,
                    max(1, (int) $challenge['expires_at'] - now()->timestamp)
                );
            }

            return response()->json([
                'success' => false,
                'message' => $attemptsRemaining > 0
                    ? 'The OTP is invalid.'
                    : 'Too many invalid attempts. Request a new OTP.',
                'attempts_remaining' => max(0, $attemptsRemaining),
            ], 422);
        }

        $member->member_type = 'Verified';
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Mobile number verified successfully.',
            'data' => [
                'profile_id' => $member->profile_id,
                'mobile_number' => $this->maskMobileNumber((string) $member->mobile_number),
                'member_type' => $member->member_type,
            ],
        ]);
    }

    private function cacheKey(string $challengeId): string
    {
        return 'api:mobile-verification:'.hash('sha256', $challengeId);
    }

    private function maskMobileNumber(string $mobileNumber): string
    {
        return str_repeat('*', max(0, strlen($mobileNumber) - 4)).substr($mobileNumber, -4);
    }
}
