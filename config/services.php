<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'nimbus' => [
        'endpoint' => env('NIMBUS_SMS_ENDPOINT', 'https://nimbusit.biz/api/SmsApi/SendMultipleApi'),
        'username' => env('NIMBUS_SMS_USERNAME'),
        'password' => env('NIMBUS_SMS_PASSWORD'),
        'sender_id' => env('NIMBUS_SMS_SENDER_ID'),
        'entity_id' => env('NIMBUS_SMS_ENTITY_ID'),
        'login_otp_template_id' => env('NIMBUS_SMS_LOGIN_OTP_TEMPLATE_ID'),
        'callback_request_template_id' => env('NIMBUS_SMS_CALLBACK_REQUEST_TEMPLATE_ID'),
        'callback_recipient' => env('NIMBUS_SMS_CALLBACK_RECIPIENT'),
        'mobile_verification_template_id' => env('NIMBUS_SMS_MOBILE_VERIFICATION_TEMPLATE_ID'),
        'interest_received_template_id' => env('NIMBUS_SMS_INTEREST_RECEIVED_TEMPLATE_ID'),
        'password_reset_otp_template_id' => env('NIMBUS_SMS_PASSWORD_RESET_OTP_TEMPLATE_ID'),
    ],

];
