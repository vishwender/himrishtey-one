<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class ApiDocumentationController extends Controller
{
    public function index(): View
    {
        $endpoints = collect(Route::getRoutes()->getRoutes())
            ->filter(fn (LaravelRoute $route): bool => str_starts_with($route->uri(), 'api/'))
            ->map(fn (LaravelRoute $route): array => $this->documentRoute($route))
            ->sortBy(fn (array $endpoint): string => $endpoint['group'].'/'.$endpoint['uri'])
            ->values();

        return view('admin.api-documentation.index', [
            'endpointGroups' => $endpoints->groupBy('group'),
            'endpointCount' => $endpoints->count(),
            'authenticatedCount' => $endpoints->where('authenticated', true)->count(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function documentRoute(LaravelRoute $route): array
    {
        $methods = collect($route->methods())->reject(fn (string $method): bool => $method === 'HEAD');
        $middleware = collect($route->gatherMiddleware())->values();
        $action = $route->getActionName();
        $feature = explode('/', preg_replace('#^api/v\d+/#', '', $route->uri()))[0] ?? 'general';

        return [
            'methods' => $methods,
            'uri' => '/'.$route->uri(),
            'name' => $route->getName(),
            'action' => $action === 'Closure' ? 'Inline handler' : class_basename($action),
            'middleware' => $middleware,
            'authenticated' => $middleware->contains('auth:sanctum'),
            'group' => str($feature)->replace('-', ' ')->title()->toString(),
            'parameters' => $this->parameters($route->uri()),
            'details' => $this->endpointDetails($route->uri(), $methods->first()),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function endpointDetails(string $uri, string $method): array
    {
        return match ($method.' '.$uri) {
            'POST api/v1/auth/forgot-password/otp/request' => [
                'description' => 'Send a password-reset OTP to a registered mobile number.',
                'request' => ['mobile_number' => '9876543210'],
                'response' => [
                    'success' => true,
                    'message' => 'Password reset OTP sent successfully.',
                    'data' => [
                        'challenge_id' => str_repeat('a', 64),
                        'expires_in' => 300,
                        'mobile_number' => '******3210',
                    ],
                ],
                'notes' => [
                    'The mobile number must be a registered ten-digit Indian mobile number.',
                    'The OTP expires after five minutes and requests are limited to five per minute.',
                    'Duplicate member accounts using the same mobile number must contact support.',
                ],
            ],
            'POST api/v1/auth/forgot-password/otp/verify' => [
                'description' => 'Verify the password-reset OTP and receive a short-lived reset token.',
                'request' => ['challenge_id' => str_repeat('a', 64), 'otp' => '1234'],
                'response' => [
                    'success' => true,
                    'message' => 'OTP verified successfully.',
                    'data' => [
                        'reset_token' => str_repeat('r', 64),
                        'expires_in' => 600,
                    ],
                ],
                'notes' => [
                    'The OTP challenge is single-use and permits five incorrect attempts.',
                    'The returned reset token expires after ten minutes.',
                    'Use the same X-App-Code throughout the reset flow.',
                ],
            ],
            'POST api/v1/auth/forgot-password/reset' => [
                'description' => 'Set a new password using the token returned by OTP verification.',
                'request' => [
                    'reset_token' => str_repeat('r', 64),
                    'password' => '<new-password>',
                    'password_confirmation' => '<new-password>',
                ],
                'response' => [
                    'success' => true,
                    'message' => 'Password reset successfully. Please log in again.',
                ],
                'notes' => [
                    'The password must contain at least eight characters and be confirmed.',
                    'The reset token is single-use and bound to the application, member, and mobile number.',
                    'A successful reset revokes the member’s existing API tokens for this application.',
                ],
            ],
            'POST api/v1/auth/login/otp/request' => [
                'description' => 'Send a four-digit login OTP to the mobile number registered with a profile ID, email address, or mobile number.',
                'request' => [
                    'login' => 'HIM12345',
                ],
                'response' => [
                    'success' => true,
                    'message' => 'OTP sent successfully.',
                    'data' => [
                        'challenge_id' => str_repeat('a', 64),
                        'expires_in' => 300,
                        'mobile_number' => '******3210',
                    ],
                ],
                'notes' => [
                    'The login field accepts a profile ID, email address, or mobile number.',
                    'The OTP expires after five minutes.',
                    'Limited to five OTP requests per minute.',
                    'A 503 response indicates that the SMS provider could not deliver the request.',
                ],
            ],
            'POST api/v1/auth/login/otp/verify' => [
                'description' => 'Verify a login OTP challenge and receive a Sanctum bearer token.',
                'request' => [
                    'challenge_id' => str_repeat('a', 64),
                    'otp' => '1234',
                ],
                'response' => [
                    'success' => true,
                    'message' => 'Login successful.',
                    'data' => [
                        'token' => '<bearer-token>',
                        'token_type' => 'Bearer',
                        'application' => [
                            'id' => 1,
                            'name' => 'Himrishtey',
                            'code' => 'himrishtey',
                        ],
                        'member' => [
                            'id' => 12345,
                            'profile_id' => 'HIM12345',
                            'full_name' => 'Example Member',
                            'email' => 'member@example.com',
                            'mobile_number' => '9876543210',
                        ],
                    ],
                ],
                'notes' => [
                    'Use the same X-App-Code value that was used to request the OTP.',
                    'A challenge is single-use and allows a maximum of five incorrect attempts.',
                    'Limited to ten verification requests per minute.',
                    'Use the returned token as Authorization: Bearer <token> on protected endpoints.',
                ],
            ],
            'POST api/v1/memberships/callback-request' => [
                'description' => 'Ask the membership team to call the authenticated member. The request is delivered to staff through SMS.',
                'request' => (object) [],
                'response' => [
                    'success' => true,
                    'message' => 'Your callback request has been sent successfully.',
                    'data' => [
                        'cooldown_seconds' => 600,
                    ],
                ],
                'notes' => [
                    'Requires a valid Sanctum bearer token and does not require a JSON body.',
                    'The SMS includes the authenticated member’s profile ID and full name.',
                    'Only one successful callback request is allowed per member every ten minutes.',
                    'A 429 response includes retry_after; a 503 response means the callback SMS could not be sent.',
                ],
            ],
            'POST api/v1/profile/mobile-verification/otp/request' => [
                'description' => 'Send a verification OTP to the authenticated member’s registered mobile number.',
                'request' => (object) [],
                'response' => [
                    'success' => true,
                    'message' => 'Verification OTP sent successfully.',
                    'data' => [
                        'challenge_id' => str_repeat('a', 64),
                        'expires_in' => 300,
                        'mobile_number' => '******3210',
                    ],
                ],
                'notes' => [
                    'Requires a valid Sanctum bearer token and does not require a JSON body.',
                    'The OTP is sent only to the mobile number currently registered on the authenticated member’s profile.',
                    'The OTP expires after five minutes and requests are limited to five per minute.',
                    'Members whose member_type is already Verified receive a 409 response.',
                ],
            ],
            'POST api/v1/profile/mobile-verification/otp/verify' => [
                'description' => 'Verify the registered mobile OTP and mark the authenticated member as verified.',
                'request' => [
                    'challenge_id' => str_repeat('a', 64),
                    'otp' => '1234',
                ],
                'response' => [
                    'success' => true,
                    'message' => 'Mobile number verified successfully.',
                    'data' => [
                        'profile_id' => 'HIM12345',
                        'mobile_number' => '******3210',
                        'member_type' => 'Verified',
                    ],
                ],
                'notes' => [
                    'The challenge is bound to the authenticated member, application, and registered mobile number.',
                    'A challenge is single-use and allows a maximum of five incorrect attempts.',
                    'Successful verification updates member_type to Verified.',
                    'Verification requests are limited to ten per minute.',
                ],
            ],
            'POST api/v1/interests/{profileId}' => [
                'description' => 'Send an interest to another member and notify that member by SMS.',
                'request' => (object) [],
                'response' => [
                    'success' => true,
                    'message' => 'Interest sent successfully.',
                    'data' => [
                        'id' => 123,
                        'profile_id' => 67890,
                        'status' => '0',
                        'sent' => true,
                        'sms_notification_sent' => true,
                        'created_at' => '2026-09-02 12:00:00',
                    ],
                ],
                'notes' => [
                    'The profileId path parameter is the target member’s numeric ID, not their displayed profile ID.',
                    'The SMS is sent to the target member’s registered mobile number and contains the sender’s displayed profile ID.',
                    'SMS is sent only when a new interest is created; an existing interest does not trigger another notification.',
                    'Interest creation remains successful if SMS delivery fails; check sms_notification_sent in the response.',
                ],
            ],
            default => $this->catalogDetails($method, $uri),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function catalogDetails(string $method, string $uri): array
    {
        $catalog = [
            'GET api/v1/about-us' => ['Get the configured About Us page.', []],
            'GET api/v1/privacy-policy' => ['Get the configured privacy policy.', []],
            'GET api/v1/refund-cancellation' => ['Get the configured refund and cancellation policy.', []],
            'GET api/v1/terms-and-conditions' => ['Get the configured terms and conditions.', []],
            'GET api/v1/test' => ['Check API connectivity and the resolved application context.', []],
            'GET api/v1/test-member-schema' => ['Development diagnostic returning member-table schema information.', [], ['This diagnostic endpoint should be disabled in production.']],
            'POST api/v1/auth/login' => ['Authenticate with profile ID, email, or mobile number and a password.', ['login' => 'HIM12345', 'password' => '<password>']],
            'POST api/v1/auth/register' => ['Create a member and begin the five-step registration workflow.', ['profile_created_for' => 'Self', 'full_name' => 'Example Member', 'email' => 'member@example.com', 'mobile_number' => '9876543210', 'gender' => 'Male', 'birth_date_time' => '1995-01-15', 'password' => '<password>']],
            'POST api/v1/auth/register/step-2/{memberId}' => ['Add birth time, height, and current location to a registration.', ['birth_time' => '10:30 AM', 'height' => '5.8', 'country_living_in' => 'India', 'state_living_in' => 'Delhi', 'city_living_in' => 'New Delhi']],
            'POST api/v1/auth/register/step-3/{memberId}' => ['Add education and career details to a registration.', ['education' => 'Graduate', 'employed_in' => 'Private', 'occupation' => 'Engineer', 'annual_income' => '10-15 LPA']],
            'POST api/v1/auth/register/step-4/{memberId}' => ['Add marital, language, religion, and horoscope details.', ['marital_status' => 'Never Married', 'mother_tongue' => 'Hindi', 'religion' => 'Hindu', 'cast' => 'Example', 'manglik' => 'No', 'horoscope_needed' => 'No']],
            'POST api/v1/auth/register/step-5/{memberId}' => ['Optionally upload a profile photo and finish registration.', ['photo' => '<jpg|jpeg|png|webp, max 5 MB>'], [], 'Multipart form data'],
            'PUT api/v1/change-password' => ['Change the authenticated member’s password.', ['current_password' => '<current-password>', 'new_password' => '<new-password>', 'new_password_confirmation' => '<new-password>']],
            'GET api/v1/home' => ['Get home-screen sections and profile recommendations for the authenticated member.', []],
            'GET api/v1/interests/sent' => ['List interests sent by the authenticated member.', []],
            'GET api/v1/interests/received' => ['List interests received by the authenticated member.', []],
            'GET api/v1/interests/{profileId}' => ['Check whether the authenticated member has sent an interest to a target member.', []],
            'PUT api/v1/interests/{id}/accept' => ['Accept an interest received by the authenticated member.', []],
            'PUT api/v1/interests/{id}/reject' => ['Reject an interest received by the authenticated member.', []],
            'DELETE api/v1/interests/{id}/cancel' => ['Cancel an interest previously sent by the authenticated member.', []],
            'GET api/v1/memberships' => ['List available membership types.', []],
            'GET api/v1/memberships/{membershipTypeId}/plans' => ['List purchasable plans for a membership type.', []],
            'GET api/v1/profile' => ['Get the authenticated member’s complete profile.', []],
            'PUT api/v1/profile/basic' => ['Update basic account and contact details.', ['full_name' => 'Example Member', 'email' => 'member@example.com', 'mobile_number' => '9876543210', 'profile_created_for' => 'Self']],
            'PUT api/v1/profile/personal' => ['Update personal, birth, physical, and marital details.', ['birth_date_time' => '1995-01-15 10:30:00', 'birth_place' => 'Delhi', 'gender' => 'Male', 'height' => '5.8', 'blood_group' => 'B+', 'marital_status' => 'Never Married', 'no_of_child' => '0', 'health_info' => '']],
            'PUT api/v1/profile/religion' => ['Update religion and community details.', ['religion' => 'Hindu', 'mother_tongue' => 'Hindi', 'cast' => 'Example', 'sub_cast' => '', 'gotra' => '', 'manglik' => 'No']],
            'PUT api/v1/profile/education-career' => ['Update education, employment, and income details.', ['about_my_education' => '', 'education' => 'Graduate', 'any_other_qualifications' => '', 'about_my_career' => '', 'employed_in' => 'Private', 'occupation' => 'Engineer', 'designation' => 'Senior Engineer', 'organization_name' => 'Example Ltd', 'job_location' => 'Delhi', 'annual_income' => '10-15 LPA']],
            'PUT api/v1/profile/location' => ['Update current and native location details.', ['country_living_in' => 'India', 'state_living_in' => 'Delhi', 'city_living_in' => 'New Delhi', 'address_living_in' => '', 'native_place' => 'Jammu']],
            'PUT api/v1/profile/family' => ['Update family background and relative details.', ['family_type' => 'Nuclear', 'family_status' => 'Middle Class', 'father_name' => '', 'father_occupation' => '', 'mother_name' => '', 'mother_occupation' => '', 'no_of_brothers' => 1, 'no_of_sisters' => 1, 'married_brothers' => 0, 'married_sisters' => 0, 'family_income' => '', 'about_family' => '']],
            'PUT api/v1/profile/lifestyle' => ['Update lifestyle and self-description details.', ['diet' => 'Vegetarian', 'is_drinking' => 'No', 'is_smoking' => 'No', 'about_me' => '', 'any_disability' => 'No']],
            'PUT api/v1/profile/partner-preferences' => ['Update partner search preferences.', ['looking_for' => 'Bride', 'partner_age_from' => 24, 'partner_age_to' => 30, 'partner_country' => 'India', 'partner_religion' => 'Hindu', 'partner_cast' => '', 'partner_height_from' => 5.2, 'partner_height_to' => 5.8, 'partner_education' => 'Graduate', 'partner_mothertongue' => 'Hindi']],
            'POST api/v1/profile/photos/gallery' => ['Upload a gallery photo for the authenticated member.', ['photo' => '<jpg|jpeg|png|webp, max 5 MB>'], [], 'Multipart form data'],
            'POST api/v1/profiles/{profileId}/contact/unlock' => ['Spend the required entitlement or wallet balance to unlock a member’s contact details.', []],
            'GET api/v1/profile-likes' => ['List profiles liked by the authenticated member.', []],
            'GET api/v1/profile-likes/{memberId}' => ['Check whether a member profile is liked.', []],
            'POST api/v1/profile-likes/{memberId}' => ['Like a member profile.', []],
            'DELETE api/v1/profile-likes/{memberId}' => ['Remove a profile like.', []],
            'POST api/v1/profile/delete-request' => ['Create a profile-deletion request.', ['reason' => 'Optional reason for deleting the profile.']],
            'GET api/v1/profile/delete-request' => ['Get the authenticated member’s latest profile-deletion request.', []],
            'POST api/v1/rate-us' => ['Submit a rating and feedback.', ['rating' => 5, 'description' => 'Great experience.']],
            'GET api/v1/search/quick' => ['Search profiles using at least one basic filter.', ['age_from' => 24, 'age_to' => 30, 'religion' => 'Hindu', 'cast' => '', 'marital_status' => 'Never Married'], [], 'Query parameters'],
            'GET api/v1/search/profile/{profileId}' => ['Find a member by displayed profile ID.', [], ['The profileId path parameter is a displayed value such as HIM12345.']],
            'GET api/v1/search/advanced' => ['Search profiles using detailed demographic, career, and location filters.', ['age_from' => 24, 'age_to' => 30, 'religion' => 'Hindu', 'mother_tongue' => 'Hindi', 'cast' => '', 'marital_status' => 'Never Married', 'height_from' => 5.2, 'height_to' => 5.8, 'country' => 'India', 'state' => 'Delhi', 'city' => 'New Delhi', 'education' => 'Graduate', 'occupation' => 'Engineer'], [], 'Query parameters'],
            'GET api/v1/shortlisted' => ['List profiles shortlisted by the authenticated member.', []],
            'GET api/v1/shortlisted/{profileId}' => ['Check whether a profile is shortlisted.', []],
            'POST api/v1/shortlisted/{profileId}' => ['Add a profile to the shortlist.', []],
            'DELETE api/v1/shortlisted/{profileId}' => ['Remove a profile from the shortlist.', []],
            'GET api/v1/success-stories/approved' => ['List approved public success stories.', []],
            'GET api/v1/success-stories' => ['List success stories submitted by the authenticated member.', []],
            'GET api/v1/success-stories/{id}' => ['Get one success story owned by the authenticated member.', []],
            'POST api/v1/success-stories' => ['Submit a success story for approval.', ['groom_name' => 'Example Groom', 'bride_name' => 'Example Bride', 'detail' => 'Our story...', 'photo' => '<jpg|jpeg|png|webp, max 5 MB>'], [], 'Multipart form data'],
            'PUT api/v1/success-stories/{id}' => ['Update a success story owned by the authenticated member.', ['groom_name' => 'Example Groom', 'bride_name' => 'Example Bride', 'detail' => 'Updated story...', 'photo' => '<optional image, max 5 MB>'], [], 'Multipart form data'],
            'DELETE api/v1/success-stories/{id}' => ['Delete a success story owned by the authenticated member.', []],
            'GET api/v1/wallet' => ['Get wallet balance, current plan, and summary information.', []],
            'GET api/v1/wallet/transactions' => ['List wallet transactions for the authenticated member.', []],
            'POST api/v1/wallet/add-money/order' => ['Create a Razorpay order for adding money to the wallet.', ['amount' => 500]],
            'POST api/v1/wallet/add-money/verify' => ['Verify a Razorpay payment and credit the wallet.', ['razorpay_order_id' => 'order_example', 'razorpay_payment_id' => 'pay_example', 'razorpay_signature' => '<signature>']],
        ];

        [$description, $request, $notes, $requestLabel] = array_pad(
            $catalog[$method.' '.$uri] ?? [
                'Call this registered API operation.',
                [],
                ['Refer to the handler and path parameters shown above.'],
            ],
            4,
            null
        );

        return [
            'description' => $description,
            'request' => empty($request) ? (object) [] : $request,
            'request_label' => $requestLabel ?? ($method === 'GET' ? 'Query parameters' : 'JSON request'),
            'response' => [
                'success' => true,
                'message' => 'Request completed successfully.',
                'data' => '<endpoint response data>',
            ],
            'notes' => $notes ?? [
                'Every request requires Accept: application/json and X-App-Code.',
                'Validation failures return HTTP 422 with an errors object.',
            ],
        ];
    }

    /**
     * @return Collection<int, string>
     */
    private function parameters(string $uri): Collection
    {
        preg_match_all('/\{([^}]+)}/', $uri, $matches);

        return collect($matches[1] ?? []);
    }
}
