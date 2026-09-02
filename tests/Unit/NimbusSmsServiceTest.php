<?php

namespace Tests\Unit;

use App\Services\NimbusSmsService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use LogicException;
use Tests\TestCase;

class NimbusSmsServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.nimbus', [
            'endpoint' => 'https://sms.example.test/send',
            'username' => 'test-user',
            'password' => 'test-password',
            'sender_id' => 'HIMRMB',
            'entity_id' => 'entity-id',
            'login_otp_template_id' => 'login-template-id',
            'callback_request_template_id' => 'callback-template-id',
            'callback_recipient' => '9999999999',
            'mobile_verification_template_id' => 'verification-template-id',
            'interest_received_template_id' => 'interest-template-id',
            'password_reset_otp_template_id' => 'password-reset-template-id',
        ]);
    }

    public function test_it_sends_a_login_otp_using_the_nimbus_template(): void
    {
        Http::fake([
            'sms.example.test/*' => Http::response(['Status' => 'OK']),
        ]);

        $sent = app(NimbusSmsService::class)->sendLoginOtp('9876543210', '1234');

        $this->assertTrue($sent);

        Http::assertSent(function (Request $request): bool {
            return str_starts_with($request->url(), 'https://sms.example.test/send?')
                && $request['UserID'] === 'test-user'
                && $request['Phno'] === '9876543210'
                && $request['Msg'] === '1234  is the OTP to login your himrishtey account.'
                && $request['TemplateID'] === 'login-template-id';
        });
    }

    public function test_it_reports_a_provider_failure(): void
    {
        Http::fake([
            'sms.example.test/*' => Http::response(['Status' => 'ERROR']),
        ]);

        $this->assertFalse(
            app(NimbusSmsService::class)->sendLoginOtp('9876543210', '1234')
        );
    }

    public function test_it_sends_a_membership_callback_request_using_its_own_template(): void
    {
        Http::fake([
            'sms.example.test/*' => Http::response(['Status' => 'OK']),
        ]);

        $sent = app(NimbusSmsService::class)->sendCallbackRequest(
            '9999999999',
            'HIM12345',
            'Example Member'
        );

        $this->assertTrue($sent);

        Http::assertSent(function (Request $request): bool {
            return $request['Phno'] === '9999999999'
                && $request['Msg'] === 'A call request from id HIM12345 regarding membership. Call back immediately Example Member.HIMRMB'
                && $request['TemplateID'] === 'callback-template-id';
        });
    }

    public function test_it_sends_a_mobile_verification_otp_using_its_own_template(): void
    {
        Http::fake([
            'sms.example.test/*' => Http::response(['Status' => 'OK']),
        ]);

        $sent = app(NimbusSmsService::class)->sendMobileVerificationOtp(
            '9876543210',
            '1234'
        );

        $this->assertTrue($sent);

        Http::assertSent(function (Request $request): bool {
            return $request['Phno'] === '9876543210'
                && $request['Msg'] === '1234 is the OTP to verify your mobile number for your himrishtey account'
                && $request['TemplateID'] === 'verification-template-id';
        });
    }

    public function test_it_sends_an_interest_notification_using_its_own_template(): void
    {
        Http::fake([
            'sms.example.test/*' => Http::response(['Status' => 'OK']),
        ]);

        $sent = app(NimbusSmsService::class)->sendInterestNotification(
            '9876543210',
            'HIM12345'
        );

        $this->assertTrue($sent);

        Http::assertSent(function (Request $request): bool {
            return $request['Phno'] === '9876543210'
                && $request['Msg'] === 'Dear user, You have got interest from a new HIM12345,Please check your account : HIMRMB'
                && $request['TemplateID'] === 'interest-template-id';
        });
    }

    public function test_it_sends_a_password_reset_otp_using_its_own_template(): void
    {
        Http::fake([
            'sms.example.test/*' => Http::response(['Status' => 'OK']),
        ]);

        $sent = app(NimbusSmsService::class)->sendPasswordResetOtp(
            '9876543210',
            '1234'
        );

        $this->assertTrue($sent);

        Http::assertSent(function (Request $request): bool {
            return $request['Phno'] === '9876543210'
                && $request['Msg'] === '1234 is the OTP to reset your Password for your himrishtey account'
                && $request['TemplateID'] === 'password-reset-template-id';
        });
    }

    public function test_it_requires_provider_credentials(): void
    {
        config()->set('services.nimbus.password');

        $this->expectException(LogicException::class);

        app(NimbusSmsService::class)->sendLoginOtp('9876543210', '1234');
    }
}
