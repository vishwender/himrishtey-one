<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use LogicException;

class NimbusSmsService
{
    /**
     * @throws ConnectionException
     */
    public function sendLoginOtp(string $mobileNumber, string $otp): bool
    {
        return $this->send(
            $mobileNumber,
            "{$otp}  is the OTP to login your himrishtey account.",
            'login_otp_template_id'
        );
    }

    /**
     * @throws ConnectionException
     */
    public function sendCallbackRequest(
        string $mobileNumber,
        string $profileId,
        string $memberName
    ): bool {
        return $this->send(
            $mobileNumber,
            "A call request from id {$profileId} regarding membership. Call back immediately {$memberName}.HIMRMB",
            'callback_request_template_id'
        );
    }

    /**
     * @throws ConnectionException
     */
    public function sendMobileVerificationOtp(string $mobileNumber, string $otp): bool
    {
        return $this->send(
            $mobileNumber,
            "{$otp} is the OTP to verify your mobile number for your himrishtey account",
            'mobile_verification_template_id'
        );
    }

    /**
     * @throws ConnectionException
     */
    public function sendInterestNotification(string $mobileNumber, string $senderProfileId): bool
    {
        return $this->send(
            $mobileNumber,
            "Dear user, You have got interest from a new {$senderProfileId},Please check your account : HIMRMB",
            'interest_received_template_id'
        );
    }

    /**
     * @throws ConnectionException
     */
    public function sendPasswordResetOtp(string $mobileNumber, string $otp): bool
    {
        return $this->send(
            $mobileNumber,
            "{$otp} is the OTP to reset your Password for your himrishtey account",
            'password_reset_otp_template_id'
        );
    }

    /**
     * @throws ConnectionException
     */
    private function send(string $mobileNumber, string $message, string $templateKey): bool
    {
        $configuration = config('services.nimbus');

        foreach (['endpoint', 'username', 'password', 'sender_id', 'entity_id', $templateKey] as $key) {
            if (blank($configuration[$key] ?? null)) {
                throw new LogicException("Nimbus SMS configuration [{$key}] is missing.");
            }
        }

        $response = Http::timeout(10)
            ->get($configuration['endpoint'], [
                'UserID' => $configuration['username'],
                'Password' => $configuration['password'],
                'SenderID' => $configuration['sender_id'],
                'Phno' => $mobileNumber,
                'Msg' => $message,
                'EntityID' => $configuration['entity_id'],
                'TemplateID' => $configuration[$templateKey],
            ]);

        if (! $response->successful()) {
            return false;
        }

        $status = $response->json('Status');

        return $status === null || strtoupper((string) $status) === 'OK';
    }
}
