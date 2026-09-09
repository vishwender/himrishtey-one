<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminActivityLog;
use App\Models\AnnualIncome;
use App\Models\Cast;
use App\Models\City;
use App\Models\Country;
use App\Models\Education;
use App\Models\Employer;
use App\Models\FamilyStatus;
use App\Models\Height;
use App\Models\MaritalStatus;
use App\Models\Member;
use App\Models\MemberRotation;
use App\Models\MembershipPlan;
use App\Models\MembershipType;
use App\Models\MotherTongue;
use App\Models\Occupation;
use App\Models\Religion;
use App\Models\SiteMember;
use App\Models\State;
use App\Services\AdminActivityLogger;
use App\Services\MemberPhotoService;
use App\Services\RelationshipManagerAccess;
use App\Services\SiteManager;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberController extends Controller
{
    /**
     * Display member listing.
     */
    public function printProfile(int $id): View
    {
        abort_unless(auth('admin')->user()?->hasRole('super-admin'), 403);

        return view('admin.members.print', ['member' => SiteMember::findOrFail($id)]);
    }

    public function assignNewMembers(Request $request): RedirectResponse
    {
        abort_unless(auth('admin')->user()?->hasPermission('edit-member')
            && ! app(RelationshipManagerAccess::class)->isRestricted(), 403);

        $validated = $request->validate([
            'member_ids' => ['required', 'array', 'min:1', 'max:100'],
            'member_ids.*' => ['required', 'integer', 'distinct'],
            'staff_id' => ['required', 'integer'],
        ]);

        $staff = Admin::query()->where('status', true)->whereNotNull('name')->where('name', '<>', '')->find($validated['staff_id']);
        abort_unless($staff, 422, 'Select an active staff user.');

        DB::connection('site')->transaction(function () use ($validated, $staff) {
            $members = SiteMember::query()->whereIn('id', $validated['member_ids'])
                ->where(fn($query) => $query->whereNull('active')->orWhere('active', ''))
                ->lockForUpdate()->get();
            abort_unless($members->count() === count($validated['member_ids']), 422, 'Some selected members are no longer new. Refresh the list and try again.');

            foreach ($members as $member) {
                $member->update(['assigned_to' => $staff->name, 'relationship_manager' => $staff->name]);
            }
        });

        return back()->with('success', 'Selected members assigned successfully.');
    }

    public function index(Request $request)
    {
        $newMembersOnly = $request->routeIs('admin.members.new');
        $bannedMembersOnly = $request->routeIs('admin.members.banned');
        $query = SiteMember::query();

        if ($bannedMembersOnly) {
            $query->where('active', 'Banned');
        }

        if ($newMembersOnly) {
            $query->where(function ($query) {
                $query->whereNull('active')
                    ->orWhere('active', '');
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->string('gender')->toString());
        }

        if ($newMembersOnly && $request->filled('assigned_to')) {
            $query->where('assigned_to', $request->string('assigned_to')->toString());
        }

        $query->addSelect([
            'membership_plan_name' => DB::connection('site')
                ->table('membership_plans')
                ->select('plan_name')
                ->whereColumn('membership_plans.id', 'members.plan_id')
                ->limit(1),
        ]);
        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('profile_id', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Active / Inactive
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            if ($request->status === 'active') {

                $query->where('active', 'Yes');
            } elseif ($request->status === 'inactive') {

                $query->where(function ($q) {

                    $q->where('active', 'No')
                        ->orWhereNull('active');
                });
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Trusted
        |--------------------------------------------------------------------------
        */

        if ($request->filled('trusted')) {

            if ($request->trusted === 'yes') {

                $query->where('is_trusted', 'Yes');
            } elseif ($request->trusted === 'no') {

                $query->where(function ($q) {

                    $q->where('is_trusted', 'No')
                        ->orWhereNull('is_trusted');
                });
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Banned Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('banned')) {

            if ($request->banned === 'yes') {

                $query->where('active', 'Banned');
            } elseif ($request->banned === 'no') {

                $query->where(function ($q) {

                    $q->where('active', '<>', 'Banned')
                        ->orWhereNull('active');
                });
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Promoted
        |--------------------------------------------------------------------------
        */

        if ($request->filled('promoted')) {

            if ($request->promoted === 'yes') {

                $query->where('promoted', 'Yes');
            } elseif ($request->promoted === 'no') {

                $query->where(function ($q) {

                    $q->where('promoted', 'No')
                        ->orWhereNull('promoted');
                });
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Visibility
        |--------------------------------------------------------------------------
        */

        if ($request->filled('visibility')) {

            if ($request->visibility === 'hidden') {

                $query->where('profile_hide', 'Yes');
            } elseif ($request->visibility === 'visible') {

                $query->where(function ($q) {

                    $q->where('profile_hide', 'No')
                        ->orWhereNull('profile_hide');
                });
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Membership Plan
        |--------------------------------------------------------------------------
        */

        if ($request->filled('plan_id')) {

            if ($request->plan_id === 'none') {

                $query->where(function ($q) {

                    $q->where('plan_id', 0)
                        ->orWhereNull('plan_id');
                });
            } else {

                $query->where('plan_id', $request->plan_id);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Relationship Manager
        |--------------------------------------------------------------------------
        */

        if ($request->filled('relationship_manager')) {
            if ($request->relationship_manager === '__unassigned') {
                $query->where(function ($query) {
                    $query->whereNull('relationship_manager')
                        ->orWhere('relationship_manager', '');
                });
            } else {
                $query->where(
                    'relationship_manager',
                    trim((string) $request->relationship_manager)
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        switch ($request->get('sort', 'newest')) {

            case 'oldest':

                $query->orderBy('id', 'asc');

                break;

            case 'name_asc':

                $query->orderBy('full_name', 'asc');

                break;

            case 'name_desc':

                $query->orderBy('full_name', 'desc');

                break;

            case 'profile_asc':

                $query->orderBy('profile_id', 'asc');

                break;

            case 'profile_desc':

                $query->orderBy('profile_id', 'desc');

                break;

            case 'newest':
            default:

                $query->orderBy('id', 'desc');

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Members
        |--------------------------------------------------------------------------
        */

        $members = $query
            ->orderByDesc('id')
            ->paginate(($newMembersOnly || $bannedMembersOnly) && in_array($request->integer('per_page', 25), [10, 25, 50, 100], true) ? $request->integer('per_page', 25) : (($newMembersOnly || $bannedMembersOnly) ? 25 : 20))
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Membership Plans
        |--------------------------------------------------------------------------
        */

        $plans = DB::connection('site')
            ->table('membership_plans')
            ->select([
                'id',
                'plan_name',
            ])
            ->orderBy('plan_name')
            ->get();

        $relationshipManagerAccess = app(RelationshipManagerAccess::class);

        $relationshipManagers = Admin::query()
            ->when(
                $relationshipManagerAccess->isRestricted(),
                fn($query) => $query->whereKey(
                    $relationshipManagerAccess->admin()?->getKey()
                )
            )
            ->select(['id', 'name', 'profile_id'])
            ->whereNotNull('name')
            ->where('name', '<>', '')
            ->orderBy('name')
            ->get();

        $assignmentStaff = collect();
        if ($newMembersOnly && auth('admin')->user()?->hasPermission('edit-member') && ! $relationshipManagerAccess->isRestricted()) {
            $assignmentStaff = Admin::query()->where('status', true)->whereNotNull('name')->where('name', '<>', '')
                ->orderBy('name')->get();
        }

        return view('admin.members.index', compact(
            'members',
            'plans',
            'relationshipManagers',
            'newMembersOnly',
            'bannedMembersOnly',
            'assignmentStaff'
        ));
    }

    /**
     * Display a member's complete profile.
     */
    public function show(
        MemberPhotoService $photoService,
        Request $request,
        $id
    ) {
        /*
    |--------------------------------------------------------------------------
    | Member
    |--------------------------------------------------------------------------
    |
    | SiteMember uses the selected site's database connection.
    |
    */

        $member = SiteMember::query()
            ->where('id', $id)
            ->first();

        if (! $member) {
            abort(404, 'Member not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Staff Activity - Member Profile Viewed
        |--------------------------------------------------------------------------
        */

        app(AdminActivityLogger::class)
            ->log(
                action: 'member_viewed',
                description: "Viewed member profile {$member->profile_id}.",
                module: 'members',
                memberId: (int) $member->id,
                subjectType: 'member',
                subjectId: (int) $member->id,
                metadata: [
                    'profile_id' => $member->profile_id,
                    'full_name' => $member->full_name,
                ]
            );

        /*
    |--------------------------------------------------------------------------
    | Membership Plans
    |--------------------------------------------------------------------------
    */

        $plans = DB::connection('site')
            ->table('membership_plans')
            ->select([
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
            ->orderBy('plan_name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Current Membership Plan
    |--------------------------------------------------------------------------
    */

        $membershipPlan = null;

        $membershipExpiryDate = null;

        if (
            ! empty($member->plan_id) &&
            (int) $member->plan_id > 0
        ) {

            $membershipPlan = $plans->firstWhere(
                'id',
                (int) $member->plan_id
            );

            /*
        |--------------------------------------------------------------------------
        | Calculate Membership Expiry
        |--------------------------------------------------------------------------
        */

            if (
                $membershipPlan &&
                ! empty($member->plan_activation_date) &&
                ! empty($membershipPlan->duration_days)
            ) {

                try {

                    $membershipExpiryDate = Carbon::parse(
                        $member->plan_activation_date
                    )->addDays(
                        max(
                            0,
                            (int) $membershipPlan->duration_days - 1
                        )
                    );
                } catch (\Exception $e) {

                    $membershipExpiryDate = null;
                }
            }
        }

        /*
|--------------------------------------------------------------------------
| Membership Usage
|--------------------------------------------------------------------------
*/

        $profileViewsUsed = 0;
        $contactViewsUsed = 0;

        $profileViewsAllowed = 0;
        $contactViewsAllowed = 0;

        $profileViewsRemaining = 0;
        $contactViewsRemaining = 0;

        if ($membershipPlan) {

            /*
    |--------------------------------------------------------------------------
    | Plan Limits
    |--------------------------------------------------------------------------
    */

            $profileViewsAllowed = max(
                0,
                (int) $membershipPlan->view_profile
            );

            $contactViewsAllowed = max(
                0,
                (int) $membershipPlan->view_contact
            );

            /*
    |--------------------------------------------------------------------------
    | Profile Views Used
    |--------------------------------------------------------------------------
    */

            $profileViewsUsed = DB::connection('site')
                ->table('profile_viewed')
                ->where('member_id', $member->id)
                ->count();

            /*
    |--------------------------------------------------------------------------
    | Contact Views Used
    |--------------------------------------------------------------------------
    */

            $contactViewsUsed = DB::connection('site')
                ->table('contact_viewed')
                ->where('member_id', $member->id)
                ->count();

            /*
    |--------------------------------------------------------------------------
    | Remaining
    |--------------------------------------------------------------------------
    */

            $profileViewsRemaining = max(
                0,
                $profileViewsAllowed - $profileViewsUsed
            );

            $contactViewsRemaining = max(
                0,
                $contactViewsAllowed - $contactViewsUsed
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Membership Payment History
        |--------------------------------------------------------------------------
        */

        $membershipPayments = DB::connection('site')
            ->table('payments')
            ->leftJoin(
                'membership_plans',
                'membership_plans.id',
                '=',
                'payments.plan_id'
            )
            ->where('payments.member_id', $member->id)
            ->select([
                'payments.id',
                'payments.payment_date',
                'payments.payment_id',
                'payments.amount',
                'payments.remarks',
                'payments.plan_id',
                'membership_plans.plan_name',
            ])
            ->orderByDesc('payments.payment_date')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Member Activity Overview
        |--------------------------------------------------------------------------
        */

        $activityCounts = [

            /*
            |--------------------------------------------------------------------------
            | Shortlisted Profiles
            |--------------------------------------------------------------------------
            */

            'shortlisted' => DB::connection('site')
                ->table('short_listed')
                ->where('member_id', $member->id)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Sent Interests
            |--------------------------------------------------------------------------
            */

            'sent_interests' => DB::connection('site')
                ->table('sent_interests')
                ->where('member_id', $member->id)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Received Interests
            |--------------------------------------------------------------------------
            */

            'received_interests' => DB::connection('site')
                ->table('sent_interests')
                ->where('profile_id', $member->id)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Profile Views
            |--------------------------------------------------------------------------
            */

            'profile_views' => DB::connection('site')
                ->table('profile_viewed')
                ->where('member_id', $member->id)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Contact Views
            |--------------------------------------------------------------------------
            */

            'contact_views' => DB::connection('site')
                ->table('contact_viewed')
                ->where('member_id', $member->id)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Wallet / Membership Payments
            |--------------------------------------------------------------------------
            */

            'wallet_payments' => DB::connection('site')
                ->table('payments')
                ->where('member_id', $member->id)
                ->count(),

        ];

        /*
            |--------------------------------------------------------------------------
            | Relationship Managers
            |--------------------------------------------------------------------------
            */

        $relationshipManagers = Admin::query()
            ->select([
                'id',
                'name',
                'email',
                'profile_id',
            ])
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Profile Photo URL
        |--------------------------------------------------------------------------
        */

        $member->photo_url = $photoService->url(
            $member->photo
        );

        /*
        |--------------------------------------------------------------------------
        | Gallery Photos
        |--------------------------------------------------------------------------
        |
        | members.photo
        |      = main profile photo
        |
        | member_photos.photo
        |      = gallery photos
        |
        */

        $galleryPhotos = DB::connection('site')
            ->table('member_photos')
            ->where('member_id', $member->id)
            ->orderByDesc('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Gallery Photo URLs
        |--------------------------------------------------------------------------
        */

        foreach ($galleryPhotos as $photo) {

            $photo->photo_url = $photoService->url(
                $photo->photo
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Profile Completion
        |--------------------------------------------------------------------------
        */

        $profileFields = [

            // Basic Information
            'full_name',
            'email',
            'mobile_number',
            'alternate_number',
            'whatsapp_number',
            'birth_date_time',
            'height',
            'gender',
            'blood_group',
            'health_info',
            'birth_place',
            'religion',
            'mother_tongue',
            'cast',
            'sub_cast',
            'gotra',
            'manglik',
            'marital_status',
            'no_of_child',

            // Education
            'about_my_education',
            'education',
            'any_other_qualifications',

            // Career
            'about_my_career',
            'employed_in',
            'occupation',
            'designation',
            'organization_name',
            'job_location',
            'annual_income',

            // Location
            'country_living_in',
            'state_living_in',
            'city_living_in',
            'address_living_in',
            'native_place',

            // Family
            'family_type',
            'family_status',
            'father_name',
            'father_occupation',
            'mother_name',
            'mother_occupation',
            'no_of_brothers',
            'no_of_sisters',
            'married_brothers',
            'married_sisters',
            'family_income',
            'about_family',

            // Lifestyle
            'diet',
            'is_drinking',
            'is_smoking',
            'about_me',
            'any_disability',

            // Partner Preferences
            'looking_for',
            'partner_age_from',
            'partner_age_to',
            'partner_country',
            'partner_religion',
            'partner_cast',
            'partner_height_from',
            'partner_height_to',
            'partner_education',
            'partner_mothertongue',
            'partner_annual_income_from',
            'partner_annual_income_to',
            'is_partner_manglik',
            'partner_occupation',
            'partner_state',
            'partner_city',
            'partner_diet',
            'is_partner_smoking',
            'is_partner_drinking',
            'about_my_partner',
        ];

        /*
        |--------------------------------------------------------------------------
        | Calculate Profile Completion
        |--------------------------------------------------------------------------
        */

        $totalFields = count($profileFields);

        $completedFields = 0;

        foreach ($profileFields as $field) {

            $value = $member->{$field} ?? null;

            if (
                $value !== null &&
                trim((string) $value) !== ''
            ) {
                $completedFields++;
            }
        }

        $profileCompletion = $totalFields > 0
            ? round(($completedFields / $totalFields) * 100)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Return URL
        |--------------------------------------------------------------------------
        */

        $returnUrl = $request->get('return');

        $shareUrl = URL::temporarySignedRoute(
            'shared-profile.show',
            now()->addDays(7),
            [
                'site' => app(SiteManager::class)->id(),
                'member' => $member->id,
            ]
        );

        $whatsappShareUrl = 'https://wa.me/?text=' . rawurlencode(
            "View {$member->full_name}'s profile: {$shareUrl}"
        );

        $remarkHistory = AdminActivityLog::query()
            ->with('admin:id,name,profile_id')
            ->where('site_id', app(SiteManager::class)->id())
            ->where('member_id', $member->id)
            ->where('action', 'remarks_updated')
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view('admin.members.show', [

            'member' => $member,
            'galleryPhotos' => $galleryPhotos,
            'profileCompletion' => $profileCompletion,
            'completedFields' => $completedFields,
            'totalFields' => $totalFields,
            'plans' => $plans,
            'membershipPlan' => $membershipPlan,
            'membershipExpiryDate' => $membershipExpiryDate,
            'profileViewsUsed' => $profileViewsUsed,
            'profileViewsAllowed' => $profileViewsAllowed,
            'profileViewsRemaining' => $profileViewsRemaining,
            'contactViewsUsed' => $contactViewsUsed,
            'contactViewsAllowed' => $contactViewsAllowed,
            'contactViewsRemaining' => $contactViewsRemaining,
            'membershipPayments' => $membershipPayments,
            'activityCounts' => $activityCounts,
            'relationshipManagers' => $relationshipManagers,
            'returnUrl' => $returnUrl,
            'shareUrl' => $shareUrl,
            'whatsappShareUrl' => $whatsappShareUrl,
            'remarkHistory' => $remarkHistory,
        ]);
    }

    public function create()
    {
        /*
    |--------------------------------------------------------------------------
    | Load Master Data
    |--------------------------------------------------------------------------
    | All these models use the dynamic "site" connection.
    | SetAdminSiteConnection middleware has already selected
    | the database for the current site.
    |--------------------------------------------------------------------------
    */

        $occupations = Occupation::query()
            ->where('status', 1)
            ->orderBy('occupation')
            ->get();

        $religions = Religion::query()
            ->orderBy('religion')
            ->get();

        $casts = Cast::query()
            ->orderBy('cast')
            ->get();

        $educations = Education::query()
            ->orderBy('education')
            ->get();

        $motherTongues = MotherTongue::query()
            ->orderBy('mother_tongue')
            ->get();

        $heights = Height::query()
            ->orderBy('height_value')
            ->get();

        $countries = Country::query()
            ->orderByRaw("CASE WHEN LOWER(name) = 'india' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();
        $maritalStatuses = MaritalStatus::query()
            ->orderBy('marital_status')
            ->get();

        $familyStatuses = FamilyStatus::query()
            ->orderBy('value')
            ->get();

        $annualIncomes = AnnualIncome::query()
            ->orderBy('display_order')
            ->orderBy('annual_income')
            ->get();

        $employers = Employer::query()
            ->orderBy('employer')
            ->get();

        $relationshipManagerAccess = app(RelationshipManagerAccess::class);
        $defaultRelationshipManager = $relationshipManagerAccess->isRestricted()
            ? (string) $relationshipManagerAccess->admin()?->name
            : '';

        $relationshipManagers = Admin::query()
            ->when(
                $relationshipManagerAccess->isRestricted(),
                fn($query) => $query->whereKey(
                    $relationshipManagerAccess->admin()?->getKey()
                )
            )
            ->select(['id', 'name', 'profile_id'])
            ->whereNotNull('name')
            ->where('name', '<>', '')
            ->orderBy('name')
            ->get();

        return view('admin.members.create', compact(
            'occupations',
            'religions',
            'casts',
            'educations',
            'motherTongues',
            'heights',
            'countries',
            'maritalStatuses',
            'familyStatuses',
            'annualIncomes',
            'employers',
            'relationshipManagers',
            'defaultRelationshipManager'
        ));
    }

    public function store(
        MemberPhotoService $photoService,
        Request $request
    ) {
        $validated = $request->validate([

            ...array_fill_keys([
                'blood_group', 'health_info', 'birth_place', 'sub_cast', 'gotra', 'manglik',
                'no_of_child', 'about_my_education', 'any_other_qualifications', 'about_my_career',
                'employed_in', 'designation', 'organization_name', 'job_location', 'annual_income',
                'address_living_in', 'native_place', 'family_type', 'family_status', 'father_name',
                'father_occupation', 'mother_name', 'mother_occupation', 'no_of_brothers',
                'no_of_sisters', 'married_brothers', 'married_sisters', 'about_family',
                'diet', 'is_drinking', 'is_smoking', 'about_me', 'any_disability', 'looking_for',
                'partner_age_from', 'partner_age_to', 'partner_country', 'partner_religion',
                'partner_cast', 'partner_height_from', 'partner_height_to', 'partner_education',
                'partner_mothertongue', 'partner_annual_income_from', 'partner_annual_income_to',
                'is_partner_manglik', 'partner_occupation', 'partner_state', 'partner_city',
                'partner_diet', 'is_partner_smoking', 'is_partner_drinking', 'about_my_partner',
                'horoscope_needed', 'active', 'member_type', 'is_trusted', 'profile_hide', 'promoted',
                'remarks',
            ], ['nullable', 'string', 'max:255']),

            'profile_created_for' => [
                'required',
                'string',
                'max:123',
            ],

            'full_name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'mobile_number' => [
                'required',
                'string',
                'max:255',
            ],

            'alternate_number' => [
                'nullable',
                'string',
                'max:233',
            ],

            'whatsapp_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'birth_date_time' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(18)->toDateTimeString(),
            ],

            'gender' => [
                'required',
                'string',
                'max:50',
            ],

            'height' => [
                'nullable',
                'string',
                'max:255',
            ],

            'religion' => [
                'nullable',
                'string',
                'max:255',
            ],

            'mother_tongue' => [
                'nullable',
                'string',
                'max:255',
            ],

            'cast' => [
                'nullable',
                'string',
                'max:255',
            ],

            'marital_status' => [
                'nullable',
                'string',
                'max:255',
            ],

            'education' => [
                'nullable',
                'string',
                'max:255',
            ],

            'occupation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'country_living_in' => [
                'nullable',
                'string',
                'max:255',
            ],

            'state_living_in' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city_living_in' => [
                'nullable',
                'string',
                'max:255',
            ],

            'family_type' => ['nullable', Rule::in(['Joint', 'Nuclear'])],
            'diet' => ['nullable', Rule::in(['Veg', 'Veg & Non Veg', 'Non Veg'])],
            'partner_diet' => ['nullable', Rule::in(['Veg', 'Veg & Non Veg', 'Non Veg'])],
            'is_drinking' => ['nullable', Rule::in(['Yes', 'No', 'Occasionally'])],
            'is_smoking' => ['nullable', Rule::in(['Yes', 'No', 'Occasionally'])],
            'is_partner_drinking' => ['nullable', Rule::in(['Yes', 'No', 'Occasionally'])],
            'is_partner_smoking' => ['nullable', Rule::in(['Yes', 'No', 'Occasionally'])],
            'manglik' => ['nullable', Rule::in(['Yes', 'No'])],
            'is_partner_manglik' => ['nullable', Rule::in(['Yes', 'No'])],
            'any_disability' => ['nullable', Rule::in(['Yes', 'No'])],
            'health_info' => ['nullable', 'string', 'max:255', 'required_if:any_disability,Yes'],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'relationship_manager' => [
                'nullable',
                'string',
                'max:255',
                Rule::exists('admins', 'name'),
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'id_proof' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ], [
            'birth_date_time.before_or_equal' => 'The member must be at least 18 years old.',
            'health_info.required_if' => 'Please describe the disability.',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Create Member
    |--------------------------------------------------------------------------
    */

        $relationshipManagerAccess = app(RelationshipManagerAccess::class);
        $relationshipManager = $relationshipManagerAccess->isRestricted()
            ? $relationshipManagerAccess->admin()?->name
            : ($validated['relationship_manager'] ?? '');

        DB::connection('site')->transaction(function () use (

            $validated,
            $relationshipManager,
            &$member
        ) {

            /*
        |--------------------------------------------------------------------------
        | Registration Date
        |--------------------------------------------------------------------------
        */

            $registrationDate = now()->format('Y-m-d H:i:s');

            /*
        |--------------------------------------------------------------------------
        | Create Member
        |--------------------------------------------------------------------------
        |
        | profile_id cannot be generated yet because the database
        | has not generated the member ID.
        |
        */

            $member = Member::create([

                'registration_date' => $registrationDate,

                // Temporary value
                'profile_id' => '0',

                'profile_created_for' => trim($validated['profile_created_for']),

                'full_name' => trim($validated['full_name']),

                'email' => trim($validated['email']),

                'mobile_number' => trim($validated['mobile_number']),

                'alternate_number' => $validated['alternate_number'] ?? '',

                'whatsapp_number' => $validated['whatsapp_number'] ?? '',

                'birth_date_time' => $validated['birth_date_time'],

                'password' => Hash::make($validated['password']),

                'height' => $validated['height'] ?? '',

                'gender' => $validated['gender'],

                'blood_group' => '',

                'health_info' => '',

                'birth_place' => '',

                'religion' => $validated['religion'] ?? '',

                'mother_tongue' => $validated['mother_tongue'] ?? '',

                'cast' => $validated['cast'] ?? '',

                'sub_cast' => '',

                'gotra' => '',

                'manglik' => '',

                'marital_status' => $validated['marital_status'] ?? '',

                'no_of_child' => '0',

                'about_my_education' => '',

                'education' => $validated['education'] ?? '',

                'any_other_qualifications' => '',

                'about_my_career' => '',

                'employed_in' => '',

                'occupation' => $validated['occupation'] ?? '',

                'designation' => '',

                'organization_name' => '',

                'job_location' => '',

                'annual_income' => '',

                'country_living_in' => $validated['country_living_in'] ?? '',

                'state_living_in' => $validated['state_living_in'] ?? '',

                'city_living_in' => $validated['city_living_in'] ?? '',

                'address_living_in' => '',

                'native_place' => '',

                'family_type' => '',

                'family_status' => '',

                'father_name' => '',

                'father_occupation' => '',

                'mother_name' => '',

                'mother_occupation' => '',

                'no_of_brothers' => '',

                'no_of_sisters' => '',

                'married_brothers' => '',

                'married_sisters' => '',

                'family_income' => '',

                'about_family' => '',

                'diet' => '',

                'is_drinking' => '',

                'is_smoking' => '',

                'about_me' => '',

                'any_disability' => '',

                'looking_for' => '',

                'partner_age_from' => '',

                'partner_age_to' => '',

                'partner_country' => '',

                'partner_religion' => '',

                'partner_cast' => '',

                'partner_height_from' => 0,

                'partner_height_to' => 0,

                'partner_education' => '',

                'partner_mothertongue' => '',

                'partner_annual_income_from' => '',

                'partner_annual_income_to' => '',

                'is_partner_manglik' => '',

                'partner_occupation' => '',

                'partner_state' => '',

                'partner_city' => '',

                'partner_diet' => '',

                'is_partner_smoking' => '',

                'is_partner_drinking' => '',

                'about_my_partner' => '',

                'horoscope_needed' => '0',

                'google_token' => '',

                'referral_code' => '',

                'id_proof' => '',

                'photo' => '',

                'photo_password' => '',

                'photo_approved' => '',

                'active' => 'No',

                'member_type' => 'free',

                'is_trusted' => 'No',

                'plan_id' => '0',

                'plan_activation_date' => now()->format('Y-m-d'),

                'profile_completed' => '0',

                'promoted' => '0',

                'remarks' => '',

                'relationship_manager' => $relationshipManager,

                'profile_hide' => 'No',

                'hide_for_days' => '0',

                'hidden_date' => '',

                'profile_view_count' => '0',

                'register_through' => 'admin',

                'weight' => '0',

                'assigned_to' => '',

                'activation_number' => 0,

                'pre_active' => 'No',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Generate Profile ID
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | Member ID = 27807
            | Profile ID = HIM27807
            |
            */

            $member->profile_id = $member->generateProfileId($member->id);

            $member->fill(collect($validated)->except([
                'password',
                'photo',
                'id_proof',
                'relationship_manager',
            ])->all());

            $member->relationship_manager = $relationshipManager;

            $member->save();
        });

        /*
        |--------------------------------------------------------------------------
        | Profile Photo
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('photo')) {
            $photoService->upload(
                (int) $member->id,
                $request->file('photo'),
                true
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ID Proof / Document
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('id_proof')) {

            $file = $request->file('id_proof');

            $filename =
                'id-proof-' .
                $member->id .
                '-' .
                Str::random(10) .
                '.' .
                $file->getClientOriginalExtension();

            $file->storeAs(
                'id_proofs',
                $filename,
                'public'
            );

            $member->update([
                'id_proof' => $filename,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.members.index')
            ->with(
                'success',
                'Member created successfully. Profile ID: ' .
                    $member->profile_id
            );
    }

    public function getStates($countryId)
    {
        $states = State::where('country_id', $countryId)
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return response()->json($states);
    }

    public function getCities($stateId)
    {
        $cities = City::where('state_id', $stateId)
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return response()->json($cities);
    }

    public function advancedSearch()
    {
        $countries = Country::on('site')
            ->orderBy('name')
            ->get();

        $religions = Religion::on('site')
            ->orderBy('religion')
            ->get();

        $casts = Cast::on('site')
            ->orderBy('cast')
            ->get();

        $educations = Education::on('site')
            ->orderBy('education')
            ->get();

        $occupations = Occupation::on('site')
            ->orderBy('occupation')
            ->get();

        $motherTongues = MotherTongue::on('site')
            ->orderBy('mother_tongue')
            ->get();

        $maritalStatuses = MaritalStatus::on('site')
            ->orderBy('marital_status')
            ->get();

        $membershipTypes = MembershipType::on('site')
            ->orderBy('plan_name')
            ->get();

        $membershipPlans = MembershipPlan::on('site')
            ->orderBy('plan_name')
            ->get();

        $relationshipManagerAccess = app(RelationshipManagerAccess::class);

        $relationshipManagers = Admin::query()
            ->when(
                $relationshipManagerAccess->isRestricted(),
                fn($query) => $query->whereKey(
                    $relationshipManagerAccess->admin()?->getKey()
                )
            )
            ->select(['id', 'name', 'profile_id'])
            ->whereNotNull('name')
            ->where('name', '<>', '')
            ->orderBy('name')
            ->get();

        return view('admin.members.advanced-search', compact(
            'countries',
            'religions',
            'casts',
            'educations',
            'occupations',
            'motherTongues',
            'maritalStatuses',
            'membershipTypes',
            'membershipPlans',
            'relationshipManagers'
        ));
    }

    public function advancedSearchResults(Request $request)
    {
        $query = Member::query();

        /*
    |--------------------------------------------------------------------------
    | Basic Search
    |--------------------------------------------------------------------------
    */

        if ($request->filled('profile_id')) {
            $query->where(
                'profile_id',
                'like',
                '%' . trim($request->profile_id) . '%'
            );
        }

        if ($request->filled('full_name')) {
            $query->where(
                'full_name',
                'like',
                '%' . trim($request->full_name) . '%'
            );
        }

        if ($request->filled('email')) {
            $query->where(
                'email',
                'like',
                '%' . trim($request->email) . '%'
            );
        }

        if ($request->filled('mobile_number')) {
            $query->where(
                'mobile_number',
                'like',
                '%' . trim($request->mobile_number) . '%'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Personal
    |--------------------------------------------------------------------------
    */

        foreach (
            [
                'profile_created_for',
                'gender',
                'religion',
                'mother_tongue',
                'cast',
                'marital_status',
                'manglik',
                'education',
                'occupation',
                'employed_in',
                'country_living_in',
                'state_living_in',
                'city_living_in',
                'family_type',
                'family_status',
                'diet',
                'is_drinking',
                'is_smoking',
                'any_disability',
            ] as $field
        ) {

            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Account
    |--------------------------------------------------------------------------
    */

        foreach (
            [
                'active',
                'member_type',
                'is_trusted',
                'promoted',
                'profile_hide',
                'register_through',
            ] as $field
        ) {

            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        if ($request->filled('relationship_manager')) {
            if ($request->relationship_manager === '__unassigned') {
                $query->where(function ($query) {
                    $query->whereNull('relationship_manager')
                        ->orWhere('relationship_manager', '');
                });
            } else {
                $query->where(
                    'relationship_manager',
                    trim((string) $request->relationship_manager)
                );
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Partner Preferences
    |--------------------------------------------------------------------------
    */

        foreach (
            [
                'partner_country',
                'partner_religion',
                'partner_cast',
                'partner_education',
                'partner_mothertongue',
                'partner_occupation',
                'partner_state',
                'partner_city',
                'partner_diet',
                'is_partner_smoking',
                'is_partner_drinking',
                'is_partner_manglik',
            ] as $field
        ) {

            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Age
    |--------------------------------------------------------------------------
    */

        if ($request->filled('age_from')) {

            $date = now()
                ->subYears((int) $request->age_from)
                ->endOfDay();

            $query->where(
                'birth_date_time',
                '<=',
                $date
            );
        }

        if ($request->filled('age_to')) {

            $date = now()
                ->subYears((int) $request->age_to + 1)
                ->addDay()
                ->startOfDay();

            $query->where(
                'birth_date_time',
                '>=',
                $date
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Partner Age
    |--------------------------------------------------------------------------
    */

        if ($request->filled('partner_age_from')) {

            $query->where(
                'partner_age_from',
                '<=',
                $request->partner_age_from
            );
        }

        if ($request->filled('partner_age_to')) {

            $query->where(
                'partner_age_to',
                '>=',
                $request->partner_age_to
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Results
    |--------------------------------------------------------------------------
    */

        $members = $query
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        /*
    |--------------------------------------------------------------------------
    | Master Data
    |--------------------------------------------------------------------------
    */

        $countries = Country::on('site')
            ->orderBy('name')
            ->get();

        $religions = Religion::on('site')
            ->orderBy('religion')
            ->get();

        $casts = Cast::on('site')
            ->orderBy('cast')
            ->get();

        $educations = Education::on('site')
            ->orderBy('education')
            ->get();

        $occupations = Occupation::on('site')
            ->orderBy('occupation')
            ->get();

        $motherTongues = MotherTongue::on('site')
            ->orderBy('mother_tongue')
            ->get();

        $maritalStatuses = MaritalStatus::on('site')
            ->orderBy('marital_status')
            ->get();

        $membershipTypes = MembershipType::on('site')
            ->orderBy('plan_name')
            ->get();

        $membershipPlans = MembershipPlan::on('site')
            ->orderBy('plan_name')
            ->get();

        $relationshipManagerAccess = app(RelationshipManagerAccess::class);

        $relationshipManagers = Admin::query()
            ->when(
                $relationshipManagerAccess->isRestricted(),
                fn($query) => $query->whereKey(
                    $relationshipManagerAccess->admin()?->getKey()
                )
            )
            ->select(['id', 'name', 'profile_id'])
            ->whereNotNull('name')
            ->where('name', '<>', '')
            ->orderBy('name')
            ->get();

        return view(
            'admin.members.advanced-search',
            compact(
                'members',
                'countries',
                'religions',
                'casts',
                'educations',
                'occupations',
                'motherTongues',
                'maritalStatuses',
                'membershipTypes',
                'membershipPlans',
                'relationshipManagers'
            )
        );
    }

    /**
     * Activate / deactivate member.
     */
    public function updateBan(Request $request, int $id): RedirectResponse
    {
        abort_unless(auth('admin')->user()?->hasRole('super-admin'), 403);
        $validated = $request->validate(['banned' => ['required', 'boolean']]);
        $member = SiteMember::query()->findOrFail($id);
        $oldValue = $member->active;
        if ($validated['banned']) {
            $member->active = 'Banned';
        } elseif ($member->active === 'Banned') {
            $member->active = 'Yes';
        }
        $member->save();

        app(AdminActivityLogger::class)->log(
            action: 'member_ban_updated',
            description: "Changed status of {$member->profile_id} from {$oldValue} to {$member->active}.",
            module: 'members',
            memberId: (int) $member->id,
            subjectType: 'member',
            subjectId: (int) $member->id,
            metadata: ['old_value' => $oldValue, 'new_value' => $member->active],
        );

        return back()->with('success', $validated['banned'] ? 'Member banned successfully.' : 'Member unbanned successfully.');
    }

    public function toggleStatus($id)
    {
        $member = SiteMember::query()
            ->where('id', $id)
            ->firstOrFail();

        /*
    |--------------------------------------------------------------------------
    | Old Status
    |--------------------------------------------------------------------------
    */

        abort_if($member->active === 'Banned', 403, 'Use the administrator unban action for banned members.');
        abort_if($member->active === 'deleted', 403, 'Deleted profiles cannot be activated here.');
        if ($member->active !== 'Yes') {
            return redirect()->route('admin.members.activation.create', $id);
        }
        $oldValue = $member->active;

        /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

        $member->active = $member->active === 'Yes'
            ? 'No'
            : 'Yes';

        $member->save();

        /*
        |--------------------------------------------------------------------------
        | Staff Activity
        |--------------------------------------------------------------------------
        */

        app(AdminActivityLogger::class)
            ->log(
                action: 'member_status_changed',
                description: "Changed status of {$member->profile_id} from {$oldValue} to {$member->active}.",
                module: 'members',
                memberId: (int) $member->id,
                subjectType: 'member',
                subjectId: (int) $member->id,
                metadata: [
                    'profile_id' => $member->profile_id,
                    'full_name' => $member->full_name,
                    'old_value' => $oldValue,
                    'new_value' => $member->active,
                ]
            );

        return back()->with(
            'success',
            $member->active === 'Yes'
                ? 'Member activated successfully.'
                : 'Member deactivated successfully.'
        );
    }

    /**
     * Mark / remove trusted status.
     */
    public function toggleTrusted($id)
    {
        $member = SiteMember::query()
            ->where('id', $id)
            ->firstOrFail();

        /*
    |--------------------------------------------------------------------------
    | Old Value
    |--------------------------------------------------------------------------
    */

        $oldValue = $member->is_trusted;

        /*
    |--------------------------------------------------------------------------
    | Toggle Trusted
    |--------------------------------------------------------------------------
    */

        $member->is_trusted =
            strtolower((string) $member->is_trusted) === 'yes'
            ? 'No'
            : 'Yes';

        $member->save();

        /*
        |--------------------------------------------------------------------------
        | Staff Activity
        |--------------------------------------------------------------------------
        */

        app(AdminActivityLogger::class)
            ->log(
                action: 'member_trusted_changed',
                description: "Changed trusted status of {$member->profile_id} from {$oldValue} to {$member->is_trusted}.",
                module: 'members',
                memberId: (int) $member->id,
                subjectType: 'member',
                subjectId: (int) $member->id,
                metadata: [
                    'profile_id' => $member->profile_id,
                    'full_name' => $member->full_name,
                    'old_value' => $oldValue,
                    'new_value' => $member->is_trusted,
                ]
            );

        return back()->with(
            'success',
            $member->is_trusted === 'Yes'
                ? 'Member marked as trusted.'
                : 'Trusted status removed.'
        );
    }

    /**
     * Hide / show member profile.
     */
    public function toggleVisibility($id)
    {
        $member = SiteMember::query()
            ->where('id', $id)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Old Value
        |--------------------------------------------------------------------------
        */

        $oldValue = $member->profile_hide;

        /*
        |--------------------------------------------------------------------------
        | Toggle Visibility
        |--------------------------------------------------------------------------
        */

        $currentlyHidden =
            strtolower((string) $member->profile_hide) === 'yes';

        if ($currentlyHidden) {

            $member->profile_hide = 'No';
            $member->hidden_date = null;
            $member->hide_for_days = null;

            $message = 'Member profile is now visible.';
        } else {

            $member->profile_hide = 'Yes';
            $member->hidden_date = now()->format('Y-m-d H:i:s');

            $message = 'Member profile has been hidden.';
        }

        $member->save();

        /*
        |--------------------------------------------------------------------------
        | Staff Activity
        |--------------------------------------------------------------------------
        */

        app(AdminActivityLogger::class)
            ->log(
                action: 'member_visibility_changed',
                description: "Changed visibility of {$member->profile_id} from {$oldValue} to {$member->profile_hide}.",
                module: 'members',
                memberId: (int) $member->id,
                subjectType: 'member',
                subjectId: (int) $member->id,
                metadata: [
                    'profile_id' => $member->profile_id,
                    'full_name' => $member->full_name,
                    'old_value' => $oldValue,
                    'new_value' => $member->profile_hide,
                    'hidden_date' => $member->hidden_date,
                    'hide_for_days' => $member->hide_for_days,
                ]
            );

        return back()->with('success', $message);
    }

    /**
     * Promote / remove promotion.
     */
    public function togglePromoted($id)
    {
        $member = SiteMember::query()
            ->where('id', $id)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Old Value
        |--------------------------------------------------------------------------
        */

        $oldValue = $member->promoted;

        /*
        |--------------------------------------------------------------------------
        | Toggle Promoted
        |--------------------------------------------------------------------------
        */

        $member->promoted =
            strtolower((string) $member->promoted) === 'yes'
            ? 'No'
            : 'Yes';

        $member->save();

        /*
        |--------------------------------------------------------------------------
        | Staff Activity
        |--------------------------------------------------------------------------
        */

        app(AdminActivityLogger::class)
            ->log(
                action: 'member_promoted_changed',
                description: "Changed promoted status of {$member->profile_id} from {$oldValue} to {$member->promoted}.",
                module: 'members',
                memberId: (int) $member->id,
                subjectType: 'member',
                subjectId: (int) $member->id,
                metadata: [
                    'profile_id' => $member->profile_id,
                    'full_name' => $member->full_name,
                    'old_value' => $oldValue,
                    'new_value' => $member->promoted,
                ]
            );

        return back()->with(
            'success',
            $member->promoted === 'Yes'
                ? 'Member profile promoted.'
                : 'Member promotion removed.'
        );
    }

    public function setProfilePhoto($memberId, $photoId)
    {
        $db = DB::connection('site');

        // Verify member exists
        $member = $db->table('members')
            ->where('id', $memberId)
            ->first();

        if (! $member) {
            abort(404, 'Member not found.');
        }

        // Get the gallery photo belonging to this member
        $photo = $db->table('member_photos')
            ->where('id', $photoId)
            ->where('member_id', $memberId)
            ->first();

        if (! $photo) {
            abort(404, 'Photo not found.');
        }

        // Set gallery photo as main profile photo
        $db->table('members')
            ->where('id', $memberId)
            ->update([
                'photo' => $photo->photo,
            ]);

        return back()->with(
            'success',
            'Profile photo updated successfully.'
        );
    }

    public function approvePhoto($memberId, $photoId)
    {
        $db = DB::connection('site');

        $photo = $db->table('member_photos')
            ->where('id', $photoId)
            ->where('member_id', $memberId)
            ->first();

        if (! $photo) {
            abort(404, 'Photo not found.');
        }

        $db->table('member_photos')
            ->where('id', $photoId)
            ->where('member_id', $memberId)
            ->update([
                'photo_approved' => 'Yes',
            ]);

        return back()->with(
            'success',
            'Photo approved successfully.'
        );
    }

    public function rejectPhoto($memberId, $photoId)
    {
        $db = DB::connection('site');

        $photo = $db->table('member_photos')
            ->where('id', $photoId)
            ->where('member_id', $memberId)
            ->first();

        if (! $photo) {
            abort(404, 'Photo not found.');
        }

        $db->table('member_photos')
            ->where('id', $photoId)
            ->where('member_id', $memberId)
            ->update([
                'photo_approved' => 'No',
            ]);

        return back()->with(
            'success',
            'Photo rejected successfully.'
        );
    }

    public function deletePhoto($memberId, $photoId)
    {
        $db = DB::connection('site');

        // Find member
        $member = $db->table('members')
            ->where('id', $memberId)
            ->first();

        if (! $member) {
            abort(404, 'Member not found.');
        }

        // Find photo belonging to this member
        $photo = $db->table('member_photos')
            ->where('id', $photoId)
            ->where('member_id', $memberId)
            ->first();

        if (! $photo) {
            abort(404, 'Photo not found.');
        }

        /*
    |--------------------------------------------------------------------------
    | Prevent deleting current profile photo
    |--------------------------------------------------------------------------
    */

        if ($member->photo === $photo->photo) {
            return back()->withErrors([
                'photo' => 'You cannot delete the current profile photo. Set another photo as the profile photo first.',
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Delete database record
    |--------------------------------------------------------------------------
    */

        $db->table('member_photos')
            ->where('id', $photoId)
            ->where('member_id', $memberId)
            ->delete();

        /*
    |--------------------------------------------------------------------------
    | Check whether the physical file is used elsewhere
    |--------------------------------------------------------------------------
    */

        $photoStillUsed = $db->table('member_photos')
            ->where('photo', $photo->photo)
            ->exists();

        /*
    |--------------------------------------------------------------------------
    | Delete physical file only if no other record uses it
    |--------------------------------------------------------------------------
    */

        if (! $photoStillUsed) {

            $photoPath = storage_path(
                'app/public/' . ltrim($photo->photo, '/')
            );

            if (is_file($photoPath)) {
                unlink($photoPath);
            }
        }

        return back()->with(
            'success',
            'Photo deleted successfully.'
        );
    }

    /**
     * Edit member profile.
     */
    public function edit(MemberPhotoService $photoService, $id)
    {
        $member = DB::connection('site')
            ->table('members')
            ->where('id', $id)
            ->first();

        if (! $member) {
            abort(404, 'Member not found.');
        }

        $member->photo_url = $photoService->url($member->photo);

        $galleryPhotos = DB::connection('site')
            ->table('member_photos')
            ->where('member_id', $member->id)
            ->orderByDesc('id')
            ->get();

        foreach ($galleryPhotos as $photo) {
            $photo->photo_url = $photoService->url($photo->photo);
        }

        $heights = Height::query()->orderBy('height_value')->get();
        $maritalStatuses = MaritalStatus::query()->orderBy('marital_status')->get();
        $religions = Religion::query()->orderBy('religion')->get();
        $motherTongues = MotherTongue::query()->orderBy('mother_tongue')->get();
        $casts = Cast::query()->orderBy('cast')->get();
        $educations = Education::query()->orderBy('education')->get();
        $employers = Employer::query()->orderBy('employer')->get();
        $occupations = Occupation::query()->where('status', 1)->orderBy('occupation')->get();
        $annualIncomes = AnnualIncome::query()->orderBy('display_order')->orderBy('annual_income')->get();
        $familyStatuses = FamilyStatus::query()->orderBy('value')->get();
        $countries = Country::query()
            ->orderByRaw("CASE WHEN LOWER(name) = 'india' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Membership Plans
    |--------------------------------------------------------------------------
    */

        $plans = DB::connection('site')
            ->table('membership_plans')
            ->select([
                'id',
                'plan_name',
                'membership_type',
                'duration_days',
                'view_contact',
                'view_profile',
                'plan_cost',
                'discount_percentage',
                'final_cost',
            ])
            ->orderBy('plan_name')
            ->get();

        $relationshipManagers = Admin::query()
            ->select([
                'id',
                'name',
                'email',
                'profile_id',
            ])
            ->orderBy('name')
            ->get();

        return view('admin.members.edit', [
            'member' => $member,
            'galleryPhotos' => $galleryPhotos,
            'heights' => $heights,
            'maritalStatuses' => $maritalStatuses,
            'religions' => $religions,
            'motherTongues' => $motherTongues,
            'casts' => $casts,
            'educations' => $educations,
            'employers' => $employers,
            'occupations' => $occupations,
            'annualIncomes' => $annualIncomes,
            'familyStatuses' => $familyStatuses,
            'countries' => $countries,
            'plans' => $plans,
            'relationshipManagers' => $relationshipManagers,
        ]);
    }

    /**
     * Update member profile.
     */
    public function update(Request $request, $id)
    {
        $db = DB::connection('site');

        /*
    |--------------------------------------------------------------------------
    | Find Member
    |--------------------------------------------------------------------------
    */

        $member = $db->table('members')
            ->where('id', $id)
            ->first();

        if (! $member) {
            abort(404, 'Member not found.');
        }

        /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([

            'full_name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'mobile_number' => [
                'required',
                'string',
                'max:255',
            ],

            'alternate_number' => [
                'nullable',
                'string',
                'max:233',
            ],

            'whatsapp_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'gender' => [
                'nullable',
                'string',
                'max:50',
            ],

            'birth_date_time' => [
                'nullable',
                'date',
                'before_or_equal:' . now()->subYears(18)->toDateTimeString(),
            ],

            'height' => [
                'nullable',
                'string',
                'max:255',
            ],

            'blood_group' => [
                'nullable',
                'string',
                'max:255',
            ],

            'religion' => [
                'nullable',
                'string',
                'max:255',
            ],

            'mother_tongue' => [
                'nullable',
                'string',
                'max:255',
            ],

            'cast' => [
                'nullable',
                'string',
                'max:255',
            ],

            'sub_cast' => [
                'nullable',
                'string',
                'max:255',
            ],

            'gotra' => [
                'nullable',
                'string',
                'max:255',
            ],

            'manglik' => [
                'nullable',
                Rule::in(['Yes', 'No']),
            ],

            'marital_status' => [
                'nullable',
                'string',
                'max:255',
            ],

            'education' => [
                'nullable',
                'string',
                'max:255',
            ],

            'employed_in' => [
                'nullable',
                'string',
                'max:255',
            ],

            'occupation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'designation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'organization_name' => [
                'nullable',
                'string',
                'max:244',
            ],

            'job_location' => [
                'nullable',
                'string',
                'max:234',
            ],

            'annual_income' => [
                'nullable',
                'string',
                'max:255',
            ],

            'country_living_in' => [
                'nullable',
                'string',
                'max:255',
            ],

            'state_living_in' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city_living_in' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address_living_in' => [
                'nullable',
                'string',
                'max:255',
            ],

            'native_place' => [
                'nullable',
                'string',
                'max:255',
            ],

            'family_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'family_status' => [
                'nullable',
                'string',
                'max:255',
            ],

            'father_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'father_occupation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'mother_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'mother_occupation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'no_of_brothers' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],
            'married_brothers' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],
            'no_of_sisters' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],
            'married_sisters' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],

            'diet' => [
                'nullable',
                Rule::in(['Veg', 'Veg & Non Veg', 'Non Veg']),
            ],

            'is_drinking' => [
                'nullable',
                Rule::in(['Yes', 'No', 'Occasionally']),
            ],

            'is_smoking' => [
                'nullable',
                Rule::in(['Yes', 'No', 'Occasionally']),
            ],

            'any_disability' => [
                'nullable',
                Rule::in(['Yes', 'No']),
            ],

            'health_info' => [
                'nullable',
                'string',
                'max:255',
                'required_if:any_disability,Yes',
            ],

            'id_proof' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'about_me' => [
                'nullable',
                'string',
            ],

            'about_family' => [
                'nullable',
                'string',
            ],

            'about_my_education' => [
                'nullable',
                'string',
                'max:244',
            ],

            'about_my_career' => [
                'nullable',
                'string',
                'max:255',
            ],

            'about_my_partner' => [
                'nullable',
                'string',
                'max:255',
            ],

            'plan_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'profile_hide' => [
                'nullable',
                'string',
                'max:255',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],

            'relationship_manager' => [
                'nullable',
                'string',
                'max:255',
                Rule::exists('admins', 'name'),
            ],

            'looking_for' => [
                'nullable',
                'string',
                'max:255',
            ],

            'partner_age_from' => [
                'nullable',
                'integer',
                'min:18',
                'max:100',
            ],

            'partner_age_to' => [
                'nullable',
                'integer',
                'min:18',
                'max:100',
            ],

            'partner_height_from' => [
                'nullable',
                'string',
                'max:255',
            ],

            'partner_height_to' => [
                'nullable',
                'string',
                'max:255',
            ],

            'partner_country' => [
                'nullable',
                'string',
                'max:255',
            ],

            'partner_religion' => [
                'nullable',
                'string',
                'max:255',
            ],

            'partner_cast' => [
                'nullable',
                'string',
                'max:255',
            ],

            'partner_education' => [
                'nullable',
                'string',
                'max:255',
            ],

            'partner_mothertongue' => [
                'nullable',
                'string',
                'max:255',
            ],

            'partner_annual_income_from' => [
                'nullable',
                'string',
                'max:255',
            ],

            'partner_annual_income_to' => [
                'nullable',
                'string',
                'max:255',
            ],

            'is_partner_manglik' => [
                'nullable',
                Rule::in(['Yes', 'No']),
            ],

            'partner_occupation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'partner_state' => [
                'nullable',
                'string',
                'max:255',
            ],

            'partner_city' => [
                'nullable',
                'string',
                'max:255',
            ],

            'partner_diet' => [
                'nullable',
                Rule::in(['Veg', 'Veg & Non Veg', 'Non Veg']),
            ],

            'is_partner_smoking' => [
                'nullable',
                Rule::in(['Yes', 'No', 'Occasionally']),
            ],

            'is_partner_drinking' => [
                'nullable',
                Rule::in(['Yes', 'No', 'Occasionally']),
            ],

            'about_my_partner' => [
                'nullable',
                'string',
                'max:255',
            ],

        ], [
            'birth_date_time.before_or_equal' => 'The member must be at least 18 years old.',
            'health_info.required_if' => 'Please describe the disability.',
        ]);

        $idProof = $request->file('id_proof');
        unset($validated['id_proof']);

        if (app(RelationshipManagerAccess::class)->isRestricted()) {
            unset($validated['relationship_manager']);
        }

        /*
        |--------------------------------------------------------------------------
        | Respect The Existing Site Schema
        |--------------------------------------------------------------------------
        |
        | Laravel converts empty form inputs to null. Some legacy site databases
        | define optional member fields as NOT NULL, so normalize only those
        | fields while leaving genuinely nullable columns untouched.
        |
        */

        $validated = $this->normalizeMemberValuesForSchema(
            $validated,
            $member,
            $db
        );

        /*
        |--------------------------------------------------------------------------
        | Detect Changed Fields
        |--------------------------------------------------------------------------
        */

        $changes = [];

        foreach ($validated as $field => $newValue) {

            $oldValue = $member->{$field} ?? null;

            /*
            |--------------------------------------------------------------------------
            | Normalize Values For Comparison
            |--------------------------------------------------------------------------
            */

            $oldCompare = is_null($oldValue)
                ? ''
                : trim((string) $oldValue);

            $newCompare = is_null($newValue)
                ? ''
                : trim((string) $newValue);

            if ($oldCompare !== $newCompare) {

                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }
        /*
        |--------------------------------------------------------------------------
        | Update Member
        |--------------------------------------------------------------------------
        */

        $db->table('members')
            ->where('id', $id)
            ->update($validated);

        if ($idProof) {
            $filename = 'id-proof-' . $member->id . '-' . Str::random(10) . '.' . $idProof->getClientOriginalExtension();
            $idProof->storeAs('id_proofs', $filename, 'public');

            $db->table('members')->where('id', $id)->update(['id_proof' => $filename]);

            $oldProof = basename((string) $member->id_proof);
            if ($oldProof !== '' && $oldProof !== $filename) {
                Storage::disk('public')->delete('id_proofs/' . $oldProof);
            }

            $changes['id_proof'] = [
                'old' => $member->id_proof,
                'new' => $filename,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Staff Activity - Member Updated
        |--------------------------------------------------------------------------
        */

        if (! empty($changes)) {

            app(AdminActivityLogger::class)
                ->log(
                    action: 'member_updated',
                    description: "Updated member profile {$member->profile_id}.",
                    module: 'members',
                    memberId: (int) $member->id,
                    subjectType: 'member',
                    subjectId: (int) $member->id,
                    metadata: [
                        'profile_id' => $member->profile_id,
                        'full_name' => $member->full_name,
                        'changes' => $changes,
                    ]
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.members.show', $id)
            ->with('success', 'Member profile updated successfully.');
    }

    /**
     * Make submitted values compatible with the selected site's members table
     * without changing that database's schema.
     */
    private function normalizeMemberValuesForSchema(
        array $values,
        object $member,
        $connection
    ): array {
        $columns = collect(
            $connection->getSchemaBuilder()->getColumns('members')
        )->keyBy('name');

        $numericTypes = [
            'bigint',
            'decimal',
            'double',
            'float',
            'int',
            'integer',
            'mediumint',
            'numeric',
            'real',
            'smallint',
            'tinyint',
        ];

        $dateTypes = [
            'date',
            'datetime',
            'time',
            'timestamp',
            'year',
        ];

        foreach ($values as $field => $value) {
            if ($value !== null) {
                continue;
            }

            $column = $columns->get($field);

            if (! $column || $column['nullable']) {
                continue;
            }

            if ($column['default'] !== null) {
                $values[$field] = $column['default'];

                continue;
            }

            $type = strtolower($column['type_name']);

            if (in_array($type, $numericTypes, true)) {
                $values[$field] = 0;
            } elseif (
                in_array($type, $dateTypes, true) ||
                in_array($type, ['enum', 'json', 'set'], true)
            ) {
                // Empty strings can be invalid for these types in strict MySQL.
                $values[$field] = $member->{$field};
            } else {
                $values[$field] = '';
            }
        }

        return $values;
    }

    public function changeMembership(
        Request $request,
        $memberId
    ) {
        /*
    |--------------------------------------------------------------------------
    | Validate
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'plan_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'plan_activation_date' => [
                'required',
                'date',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Selected Site Connection
    |--------------------------------------------------------------------------
    */

        $db = DB::connection('site');

        /*
    |--------------------------------------------------------------------------
    | Find Member
    |--------------------------------------------------------------------------
    */

        $member = $db->table('members')
            ->where('id', $memberId)
            ->first();

        if (! $member) {
            abort(404, 'Member not found.');
        }

        /*
    |--------------------------------------------------------------------------
    | Find Plan
    |--------------------------------------------------------------------------
    */

        $plan = $db->table('membership_plans')
            ->where('id', $validated['plan_id'])
            ->first();

        if (! $plan) {

            return back()
                ->withErrors([
                    'plan_id' => 'The selected membership plan does not exist.',
                ])
                ->withInput();
        }

        /*
    |--------------------------------------------------------------------------
    | Calculate Expiry
    |--------------------------------------------------------------------------
    */

        $activationDate = Carbon::parse(
            $validated['plan_activation_date']
        );

        $expiryDate = $activationDate->copy()->addDays(
            max(
                0,
                (int) $plan->duration_days - 1
            )
        );

        /*
    |--------------------------------------------------------------------------
    | Update Member
    |--------------------------------------------------------------------------
    */

        $db->table('members')
            ->where('id', $memberId)
            ->update([
                'plan_id' => $plan->id,
                'plan_activation_date' => $activationDate->format('Y-m-d'),
            ]);

        /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin.members.show', [
                'id' => $memberId,
            ])
            ->with(
                'success',
                "Membership changed to {$plan->plan_name} successfully."
            );
    }

    public function updateRelationshipManager(
        Request $request,
        $memberId
    ) {
        $validated = $request->validate([
            'relationship_manager' => [
                'nullable',
                'string',
                'max:255',
                Rule::exists('admins', 'name'),
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Find Member
    |--------------------------------------------------------------------------
    */

        $member = DB::connection('site')
            ->table('members')
            ->where('id', $memberId)
            ->first();

        if (! $member) {
            abort(404, 'Member not found.');
        }

        /*
    |--------------------------------------------------------------------------
    | Update Relationship Manager
    |--------------------------------------------------------------------------
    */

        DB::connection('site')
            ->table('members')
            ->where('id', $memberId)
            ->update([
                'relationship_manager' => $validated['relationship_manager'] ?? null,
            ]);

        /*
    |--------------------------------------------------------------------------
    | Return
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->back()
            ->with(
                'success',
                'Relationship manager updated successfully.'
            );
    }

    public function updateRemarks(Request $request, $memberId)
    {
        /*
    |--------------------------------------------------------------------------
    | Validate
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'remarks' => [
                'required',
                'string',
                'max:10000',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Find Member
    |--------------------------------------------------------------------------
    */

        $member = DB::connection('site')
            ->table('members')
            ->where('id', $memberId)
            ->first();

        if (! $member) {
            abort(404, 'Member not found.');
        }

        /*
    |--------------------------------------------------------------------------
    | Old / New Remark
    |--------------------------------------------------------------------------
    */

        $oldRemarks = $member->remarks;

        $newRemarks = $validated['remarks'] ?? null;

        /*
    |--------------------------------------------------------------------------
    | Update Remarks
    |--------------------------------------------------------------------------
    */

        DB::connection('site')
            ->table('members')
            ->where('id', $memberId)
            ->update([
                'remarks' => $newRemarks,
            ]);

        /*
    |--------------------------------------------------------------------------
    | Staff Activity
    |--------------------------------------------------------------------------
    */

        app(AdminActivityLogger::class)
            ->log(
                action: 'remarks_updated',
                description: "Updated remarks for {$member->profile_id}.",
                module: 'members',
                memberId: (int) $member->id,
                subjectType: 'member',
                subjectId: (int) $member->id,
                metadata: [
                    'profile_id' => $member->profile_id,
                    'full_name' => $member->full_name,
                    'old_remarks' => $oldRemarks,
                    'new_remarks' => $newRemarks,
                    'remark_type' => $member->active === 'Yes'
                        ? 'RM Remarks'
                        : 'Assigned Remarks',
                ]
            );

        /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->back()
            ->with(
                'success',
                'Member remarks updated successfully.'
            );
    }

    public function storeRotation(Request $request, $memberId)
    {
        $validated = $request->validate([
            'days' => [
                'required',
                'integer',
                'min:1',
                'max:365',
            ],

            'time' => [
                'required',
                'date_format:H:i',
            ],
        ]);

        $member = Member::findOrFail($memberId);

        /*
    |--------------------------------------------------------------------------
    | Calculate Next Rotation
    |--------------------------------------------------------------------------
    */

        $nextRotationAt = Carbon::today()
            ->addDays((int) $validated['days'])
            ->setTimeFromTimeString($validated['time']);

        /*
        |--------------------------------------------------------------------------
        | Save Rotation
        |--------------------------------------------------------------------------
        */

        MemberRotation::create([

            'member_id' => $member->id,

            'user_id' => auth('admin')->id(),

            'days' => $validated['days'],

            'time' => $validated['time'],

            'next_rotation_at' => $nextRotationAt,

            'status' => 'pending',

        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Rotation scheduled for ' .
                    $nextRotationAt->format('d M Y h:i A')
            );
    }

    public function createRotation(Request $request, $memberId)
    {
        $validated = $request->validate([
            'days' => [
                'required',
                'integer',
                'min:1',
            ],

            'time' => [
                'required',
                'date_format:H:i',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Check Member
    |--------------------------------------------------------------------------
    */

        $member = Member::findOrFail($memberId);

        /*
    |--------------------------------------------------------------------------
    | Calculate Next Rotation
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | Today = 18 August
    | Days  = 7
    | Time  = 10:30
    |
    | Next rotation = 25 August 10:30
    |
    */

        $nextRotationAt = Carbon::today()
            ->addDays((int) $validated['days'])
            ->setTimeFromTimeString($validated['time']);

        /*
    |--------------------------------------------------------------------------
    | Create Rotation
    |--------------------------------------------------------------------------
    */

        MemberRotation::create([
            'member_id' => $member->id,

            'user_id' => auth('admin')->id(),

            'days' => $validated['days'],

            'time' => $validated['time'],

            'next_rotation_at' => $nextRotationAt,

            'status' => 'pending',

            'completed_at' => null,
        ]);

        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->back()
            ->with(
                'success',
                'Member rotation scheduled successfully for ' .
                    $nextRotationAt->format('d M Y h:i A')
            );
    }
}
