<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RequestPasswordResetOtpRequest;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\VerifyPasswordResetOtpRequest;
use App\Models\Member;
use App\Models\PersonalAccessToken;
use App\Services\NimbusSmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class ForgotPasswordController extends Controller
{
    private const int OTP_EXPIRY_MINUTES = 5;

    private const int RESET_TOKEN_EXPIRY_MINUTES = 10;

    private const int OTP_MAX_ATTEMPTS = 5;

    public function requestOtp(
        RequestPasswordResetOtpRequest $request,
        NimbusSmsService $smsService
    ): JsonResponse {
        $mobileNumber = $request->string('mobile_number')->toString();
        $members = Member::query()->where('mobile_number', $mobileNumber)->get();

        if ($members->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No account was found with this mobile number.',
            ], 404);
        }

        if ($members->count() > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Multiple accounts use this mobile number. Please contact support.',
            ], 409);
        }

        $member = $members->first();
        $application = $request->attributes->get('application');
        $otp = (string) random_int(1000, 9999);

        try {
            $sent = $smsService->sendPasswordResetOtp($mobileNumber, $otp);
        } catch (Throwable $exception) {
            report($exception);
            $sent = false;
        }

        if (! $sent) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to send the password reset OTP. Please try again.',
            ], 503);
        }

        $challengeId = Str::random(64);
        $expiresAt = now()->addMinutes(self::OTP_EXPIRY_MINUTES);

        Cache::put($this->otpCacheKey($challengeId), [
            'application_id' => (string) $application->id,
            'member_id' => $member->id,
            'mobile_number' => $mobileNumber,
            'otp_hash' => Hash::make($otp),
            'attempts_remaining' => self::OTP_MAX_ATTEMPTS,
            'expires_at' => $expiresAt->timestamp,
        ], $expiresAt);

        return response()->json([
            'success' => true,
            'message' => 'Password reset OTP sent successfully.',
            'data' => [
                'challenge_id' => $challengeId,
                'expires_in' => self::OTP_EXPIRY_MINUTES * 60,
                'mobile_number' => $this->maskMobileNumber($mobileNumber),
            ],
        ]);
    }

    public function verifyOtp(VerifyPasswordResetOtpRequest $request): JsonResponse
    {
        $challengeId = $request->string('challenge_id')->toString();
        $cacheKey = $this->otpCacheKey($challengeId);
        $challenge = Cache::pull($cacheKey);
        $application = $request->attributes->get('application');

        if (
            ! is_array($challenge)
            || ($challenge['expires_at'] ?? 0) < now()->timestamp
            || ! hash_equals((string) $challenge['application_id'], (string) $application->id)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'The password reset challenge is invalid or has expired.',
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

        $member = Member::query()->find($challenge['member_id']);

        if (! $member || ! hash_equals((string) $challenge['mobile_number'], (string) $member->mobile_number)) {
            return response()->json([
                'success' => false,
                'message' => 'The password reset challenge is invalid or has expired.',
            ], 422);
        }

        $resetToken = Str::random(64);
        $expiresAt = now()->addMinutes(self::RESET_TOKEN_EXPIRY_MINUTES);

        Cache::put($this->resetTokenCacheKey($resetToken), [
            'application_id' => (string) $application->id,
            'member_id' => $member->id,
            'mobile_number' => (string) $member->mobile_number,
            'expires_at' => $expiresAt->timestamp,
        ], $expiresAt);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'data' => [
                'reset_token' => $resetToken,
                'expires_in' => self::RESET_TOKEN_EXPIRY_MINUTES * 60,
            ],
        ]);
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $resetToken = $request->string('reset_token')->toString();
        $reset = Cache::pull($this->resetTokenCacheKey($resetToken));
        $application = $request->attributes->get('application');

        if (
            ! is_array($reset)
            || ($reset['expires_at'] ?? 0) < now()->timestamp
            || ! hash_equals((string) $reset['application_id'], (string) $application->id)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'The password reset token is invalid or has expired.',
            ], 422);
        }

        $member = Member::query()->find($reset['member_id']);

        if (! $member || ! hash_equals((string) $reset['mobile_number'], (string) $member->mobile_number)) {
            return response()->json([
                'success' => false,
                'message' => 'The password reset token is invalid or has expired.',
            ], 422);
        }

        $member->password = Hash::make($request->string('password')->toString());
        $member->save();

        PersonalAccessToken::query()
            ->where('application_id', $application->id)
            ->where('tokenable_id', $member->id)
            ->where('tokenable_type', $member->getMorphClass())
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. Please log in again.',
        ]);
    }

    private function otpCacheKey(string $challengeId): string
    {
        return 'api:password-reset-otp:'.hash('sha256', $challengeId);
    }

    private function resetTokenCacheKey(string $resetToken): string
    {
        return 'api:password-reset-token:'.hash('sha256', $resetToken);
    }

    private function maskMobileNumber(string $mobileNumber): string
    {
        return str_repeat('*', max(0, strlen($mobileNumber) - 4)).substr($mobileNumber, -4);
    }
}
