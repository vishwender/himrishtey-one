@extends('admin.layout')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Edit Member
            </h4>

            <div class="text-muted">

                {{ $member->full_name }}

                <span class="mx-2">|</span>

                Profile ID:
                <strong>{{ $member->profile_id }}</strong>

            </div>

        </div>


        <div>

            <a
                href="{{ route('admin.members.show', $member->id) }}"
                class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left me-1"></i>
                Back to Profile

            </a>

        </div>

    </div>


    {{-- Validation Errors --}}

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())

    <div class="alert alert-danger">

        <strong>Please correct the following:</strong>

        <ul class="mb-0 mt-2">

            @foreach($errors->all() as $error)

            <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

    @endif


    <form
        id="member-edit-form"
        action="{{ route('admin.members.update', $member->id) }}"
        method="POST"
        enctype="multipart/form-data"
        data-states-url="{{ route('admin.members.location.states', ['countryId' => '__ID__']) }}"
        data-cities-url="{{ route('admin.members.location.cities', ['stateId' => '__ID__']) }}">

        @csrf
        @method('PUT')


        {{-- =====================================================
            Basic Information
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4 member-section">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="bi bi-person me-2"></i>
                    Basic Information
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Full Name
                        </label>

                        <input
                            type="text"
                            name="full_name"
                            class="form-control"
                            value="{{ old('full_name', $member->full_name) }}"
                            required>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $member->email) }}"
                            required>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Mobile Number
                        </label>

                        <input
                            type="text"
                            name="mobile_number"
                            class="form-control"
                            value="{{ old('mobile_number', $member->mobile_number) }}"
                            required>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Alternate Number
                        </label>

                        <input
                            type="text"
                            name="alternate_number"
                            class="form-control"
                            value="{{ old('alternate_number', $member->alternate_number) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            WhatsApp Number
                        </label>

                        <input
                            type="text"
                            name="whatsapp_number"
                            class="form-control"
                            value="{{ old('whatsapp_number', $member->whatsapp_number) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Gender
                        </label>

                        <input
                            type="text"
                            name="gender"
                            class="form-control"
                            value="{{ old('gender', $member->gender) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label" for="birth_date_time">
                            Birth Date and Time
                        </label>

                        <input
                            type="datetime-local"
                            id="birth_date_time"
                            name="birth_date_time"
                            class="form-control"
                            value="{{ old('birth_date_time', $member->birth_date_time ? \Carbon\Carbon::parse($member->birth_date_time)->format('Y-m-d\TH:i') : '') }}"
                            max="{{ now()->subYears(18)->format('Y-m-d\TH:i') }}">
                        <div class="form-text">The member must be at least 18 years old.</div>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Height
                        </label>

                        <select name="height" class="form-select">
                            <option value="">Select Height</option>
                            @foreach($heights as $height)
                            <option value="{{ $height->height_value ?? $height->height }}" @selected(old('height', $member->height) == ($height->height_value ?? $height->height))>{{ \App\Support\HeightFormatter::format($height->height) }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Blood Group
                        </label>

                        <input
                            type="text"
                            name="blood_group"
                            class="form-control"
                            value="{{ old('blood_group', $member->blood_group) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Marital Status
                        </label>

                        <select name="marital_status" class="form-select">
                            <option value="">Select Marital Status</option>
                            @foreach($maritalStatuses as $status)
                            <option value="{{ $status->marital_status }}" @selected(old('marital_status', $member->marital_status) == $status->marital_status)>{{ $status->marital_status }}</option>
                            @endforeach
                        </select>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            Religion & Community
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4 member-section">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="bi bi-heart me-2"></i>
                    Religion & Community
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Religion
                        </label>

                        <select name="religion" class="form-select">
                            <option value="">Select Religion</option>
                            @foreach($religions as $religion)
                            <option value="{{ $religion->religion }}" @selected(old('religion', $member->religion) == $religion->religion)>{{ $religion->religion }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Mother Tongue
                        </label>

                        <select name="mother_tongue" class="form-select">
                            <option value="">Select Mother Tongue</option>
                            @foreach($motherTongues as $motherTongue)
                            <option value="{{ $motherTongue->mother_tongue }}" @selected(old('mother_tongue', $member->mother_tongue) == $motherTongue->mother_tongue)>{{ $motherTongue->mother_tongue }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Caste
                        </label>

                        <select name="cast" class="form-select">
                            <option value="">Select Caste</option>
                            @foreach($casts as $cast)
                            <option value="{{ $cast->cast }}" @selected(old('cast', $member->cast) == $cast->cast)>{{ $cast->cast }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Sub Caste
                        </label>

                        <input
                            type="text"
                            name="sub_cast"
                            class="form-control"
                            value="{{ old('sub_cast', $member->sub_cast) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Gotra
                        </label>

                        <input
                            type="text"
                            name="gotra"
                            class="form-control"
                            value="{{ old('gotra', $member->gotra) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Manglik
                        </label>

                        <select name="manglik" class="form-select">
                            <option value="">Select</option>
                            @foreach(['Yes', 'No'] as $value)
                            <option value="{{ $value }}" @selected(old('manglik', $member->manglik) == $value)>{{ $value }}</option>
                            @endforeach
                        </select>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            Education
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4 member-section">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="bi bi-mortarboard me-2"></i>
                    Education
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Education
                        </label>

                        <select name="education" class="form-select">
                            <option value="">Select Education</option>
                            @if(old('education', $member->education) && ! $educations->contains('education', old('education', $member->education)))
                                <option selected value="{{ old('education', $member->education) }}">{{ old('education', $member->education) }}</option>
                            @endif
                            @foreach($educations as $education)
                            <option value="{{ $education->education }}" @selected(old('education', $member->education) == $education->education)>{{ $education->education }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Other Qualifications
                        </label>

                        <input
                            type="text"
                            name="any_other_qualifications"
                            class="form-control"
                            value="{{ old('any_other_qualifications', $member->any_other_qualifications) }}">

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            About Education
                        </label>

                        <textarea
                            name="about_my_education"
                            rows="3"
                            class="form-control">{{ old('about_my_education', $member->about_my_education) }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            Career
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4 member-section">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="bi bi-briefcase me-2"></i>
                    Career
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Employed In
                        </label>

                        <select name="employed_in" class="form-select">
                            <option value="">Select Employer Type</option>
                            @foreach($employers as $employer)
                            <option value="{{ $employer->employer }}" @selected(old('employed_in', $member->employed_in) == $employer->employer)>{{ $employer->employer }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Occupation
                        </label>

                        <select name="occupation" class="form-select">
                            <option value="">Select Occupation</option>
                            @foreach($occupations as $occupation)
                            <option value="{{ $occupation->occupation }}" @selected(old('occupation', $member->occupation) == $occupation->occupation)>{{ $occupation->occupation }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Designation
                        </label>

                        <input
                            type="text"
                            name="designation"
                            class="form-control"
                            value="{{ old('designation', $member->designation) }}">

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Organization
                        </label>

                        <input
                            type="text"
                            name="organization_name"
                            class="form-control"
                            value="{{ old('organization_name', $member->organization_name) }}">

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Job Location
                        </label>

                        <input
                            type="text"
                            name="job_location"
                            class="form-control"
                            value="{{ old('job_location', $member->job_location) }}">

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Annual Income
                        </label>

                        <select name="annual_income" class="form-select">
                            <option value="">Select Annual Income</option>
                            @foreach($annualIncomes as $income)
                            <option value="{{ $income->annual_income }}" @selected(old('annual_income', $member->annual_income) == $income->annual_income)>{{ $income->annual_income }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            About Career
                        </label>

                        <textarea
                            name="about_my_career"
                            rows="3"
                            class="form-control">{{ old('about_my_career', $member->about_my_career) }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            Location
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4 member-section">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="bi bi-geo-alt me-2"></i>
                    Location
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Country
                        </label>

                        <select name="country_living_in" id="country_living_in" class="form-select">
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                            <option value="{{ $country->name }}" data-id="{{ $country->id }}" @selected(old('country_living_in', $member->country_living_in) == $country->name)>{{ $country->name }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            State
                        </label>

                        <select name="state_living_in" id="state_living_in" class="form-select" data-current="{{ old('state_living_in', $member->state_living_in) }}">
                            @if(old('state_living_in', $member->state_living_in))
                                <option selected value="{{ old('state_living_in', $member->state_living_in) }}">{{ old('state_living_in', $member->state_living_in) }}</option>
                            @endif
                            <option value="">Select Country First</option>
                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            City
                        </label>

                        <select name="city_living_in" id="city_living_in" class="form-select" data-current="{{ old('city_living_in', $member->city_living_in) }}">
                            @if(old('city_living_in', $member->city_living_in))
                                <option selected value="{{ old('city_living_in', $member->city_living_in) }}">{{ old('city_living_in', $member->city_living_in) }}</option>
                            @endif
                            <option value="">Select State First</option>
                        </select>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Native Place
                        </label>

                        <input
                            type="text"
                            name="native_place"
                            class="form-control"
                            value="{{ old('native_place', $member->native_place) }}">

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Address
                        </label>

                        <input
                            type="text"
                            name="address_living_in"
                            class="form-control"
                            value="{{ old('address_living_in', $member->address_living_in) }}">

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            Family
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4 member-section">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="bi bi-house-heart me-2"></i>
                    Family
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Family Type
                        </label>

                        <select
                            name="family_type"
                            id="family_type"
                            class="form-select">

                            <option value="">
                                Select
                            </option>

                            <option
                                value="Joint"
                                @selected(old('family_type', $member->family_type)=='Joint' )>

                                Joint

                            </option>

                            <option
                                value="Nuclear"
                                @selected(old('family_type', $member->family_type)=='Nuclear' )>

                                Nuclear

                            </option>

                        </select>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Family Status
                        </label>

                        <select name="family_status" class="form-select">
                            <option value="">Select Family Status</option>
                            @foreach($familyStatuses as $status)
                            <option value="{{ $status->value }}" @selected(old('family_status', $member->family_status) == $status->value)>{{ $status->value }}</option>
                            @endforeach
                        </select>

                    </div>



                    <div class="col-md-6">

                        <label class="form-label">
                            Father Name
                        </label>

                        <input
                            type="text"
                            name="father_name"
                            class="form-control"
                            value="{{ old('father_name', $member->father_name) }}">

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Father Occupation
                        </label>

                        <input
                            type="text"
                            name="father_occupation"
                            class="form-control"
                            value="{{ old('father_occupation', $member->father_occupation) }}">

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Mother Name
                        </label>

                        <input
                            type="text"
                            name="mother_name"
                            class="form-control"
                            value="{{ old('mother_name', $member->mother_name) }}">

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Mother Occupation
                        </label>

                        <input
                            type="text"
                            name="mother_occupation"
                            class="form-control"
                            value="{{ old('mother_occupation', $member->mother_occupation) }}">

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Brother
                        </label>

                        <select name="no_of_brothers" id="no_of_brothers" class="form-select">
                            <option value="">Select</option>
                            @foreach(range(0, 5) as $number)
                            <option value="{{ $number }}" @selected(old('no_of_brothers', $member->no_of_brothers) == $number)>{{ $number }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Married Brother
                        </label>

                        <select name="married_brothers" id="married_brothers" class="form-select">
                            <option value="">Select</option>
                            @foreach(range(0, 5) as $number)
                            <option value="{{ $number }}" @selected(old('married_brothers', $member->married_brothers) == $number)>{{ $number }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="married_brothers_zero" value="0" disabled>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Sister
                        </label>

                        <select name="no_of_sisters" id="no_of_sisters" class="form-select">
                            <option value="">Select</option>
                            @foreach(range(0, 5) as $number)
                            <option value="{{ $number }}" @selected(old('no_of_sisters', $member->no_of_sisters) == $number)>{{ $number }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Married Sister
                        </label>

                        <select name="married_sisters" id="married_sisters" class="form-select">
                            <option value="">Select</option>
                            @foreach(range(0, 5) as $number)
                            <option value="{{ $number }}" @selected(old('married_sisters', $member->married_sisters) == $number)>{{ $number }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="married_sisters_zero" value="0" disabled>

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            About Family
                        </label>

                        <textarea
                            name="about_family"
                            rows="3"
                            class="form-control">{{ old('about_family', $member->about_family) }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            Lifestyle
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4 member-section">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    Lifestyle
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Diet
                        </label>

                        <select name="diet" class="form-select">
                            <option value="">Select Diet</option>
                            @foreach(['Veg', 'Veg & Non Veg', 'Non Veg'] as $value)
                            <option value="{{ $value }}" @selected(old('diet', $member->diet) == $value)>{{ $value }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Drinking
                        </label>

                        <select name="is_drinking" class="form-select">
                            <option value="">Select</option>
                            @foreach(['Yes', 'No', 'Occasionally'] as $value)
                            <option value="{{ $value }}" @selected(old('is_drinking', $member->is_drinking) == $value)>{{ $value }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Smoking
                        </label>

                        <select name="is_smoking" class="form-select">
                            <option value="">Select</option>
                            @foreach(['Yes', 'No', 'Occasionally'] as $value)
                            <option value="{{ $value }}" @selected(old('is_smoking', $member->is_smoking) == $value)>{{ $value }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Disability
                        </label>

                        <select name="any_disability" id="any_disability" class="form-select">
                            <option value="">Select</option>
                            @foreach(['Yes', 'No'] as $value)
                            <option value="{{ $value }}" @selected(old('any_disability', $member->any_disability) == $value)>{{ $value }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-6 {{ old('any_disability', $member->any_disability) === 'Yes' ? '' : 'd-none' }}" id="disability_description_group">
                        <label for="health_info" class="form-label">Describe Disability</label>
                        <input type="text" name="health_info" id="health_info" class="form-control"
                            value="{{ old('health_info', $member->health_info ?? '') }}" maxlength="255">
                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            About Me
                        </label>

                        <textarea
                            name="about_me"
                            rows="4"
                            class="form-control">{{ old('about_me', $member->about_me) }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            Admin / Membership
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4 member-section">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="bi bi-shield-check me-2"></i>
                    Admin & Membership
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Membership Plan
                        </label>

                        <select
                            name="plan_id"
                            class="form-select">

                            <option value="">
                                No Plan
                            </option>

                            @foreach($plans as $plan)

                            <option
                                value="{{ $plan->id }}"
                                {{ (string) old('plan_id', $member->plan_id) === (string) $plan->id ? 'selected' : '' }}>

                                {{ $plan->plan_name }}

                                @if($plan->membership_type)
                                — {{ $plan->membership_type }}
                                @endif

                            </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Profile Visibility
                        </label>

                        <select
                            name="profile_hide"
                            class="form-select">

                            <option
                                value="No"
                                {{ old('profile_hide', $member->profile_hide) === 'No' ? 'selected' : '' }}>
                                Visible
                            </option>

                            <option
                                value="Yes"
                                {{ old('profile_hide', $member->profile_hide) === 'Yes' ? 'selected' : '' }}>
                                Hidden
                            </option>

                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Relationship Manager
                        </label>

                        @if(app(\App\Services\RelationshipManagerAccess::class)->isRestricted())

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $member->relationship_manager }}"
                            disabled>

                        @else

                        <select
                            name="relationship_manager"
                            class="form-select @error('relationship_manager') is-invalid @enderror">

                            <option value="">
                                — No Relationship Manager —
                            </option>

                            @foreach($relationshipManagers as $manager)

                            <option
                                value="{{ $manager->name }}"
                                {{ old('relationship_manager', $member->relationship_manager) === $manager->name ? 'selected' : '' }}>
                                {{ $manager->name }} ({{ $manager->profile_id }} · {{ $manager->email }})
                            </option>

                            @endforeach

                        </select>

                        @error('relationship_manager')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                        @enderror

                        @endif

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            Admin Remarks
                        </label>

                        <textarea
                            name="remarks"
                            rows="3"
                            class="form-control">{{ old('remarks', $member->remarks) }}</textarea>

                    </div>

                </div>

            </div>

        </div>

        {{-- =========================================================
    PARTNER PREFERENCES
========================================================= --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-0 pt-4 px-4">

                <div class="d-flex align-items-center">

                    <div
                        class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3"
                        style="width:42px;height:42px;">

                        <i class="bi bi-heart fs-5"></i>

                    </div>

                    <div>
                        <h5 class="mb-1">
                            Partner Preferences
                        </h5>

                        <small class="text-muted">
                            Preferences for the member's desired partner
                        </small>
                    </div>

                </div>

            </div>


            <div class="card-body px-4 pb-4">


                {{-- =====================================================
            BASIC PREFERENCES
        ====================================================== --}}

                <h6 class="text-primary border-bottom pb-2 mb-3">
                    <i class="bi bi-person-heart me-2"></i>
                    Basic Preferences
                </h6>


                <div class="row g-3">


                    {{-- Looking For --}}
                    <div class="col-md-6">

                        <label for="looking_for" class="form-label">
                            Looking For
                        </label>

                        <select name="looking_for" id="looking_for" class="form-select">
                            <option value="">Select Marital Status</option>
                            @foreach($maritalStatuses as $status)
                            <option value="{{ $status->marital_status }}" @selected(old('looking_for', $member->looking_for) == $status->marital_status)>{{ $status->marital_status }}</option>
                            @endforeach
                        </select>

                    </div>


                    {{-- Partner Age From --}}
                    <div class="col-md-3">

                        <label for="partner_age_from" class="form-label">
                            Age From
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            id="partner_age_from"
                            name="partner_age_from"
                            value="{{ old('partner_age_from', $member->partner_age_from) }}"
                            min="18"
                            max="100"
                            placeholder="Example: 25">

                    </div>


                    {{-- Partner Age To --}}
                    <div class="col-md-3">

                        <label for="partner_age_to" class="form-label">
                            Age To
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            id="partner_age_to"
                            name="partner_age_to"
                            value="{{ old('partner_age_to', $member->partner_age_to) }}"
                            min="18"
                            max="100"
                            placeholder="Example: 30">

                    </div>


                    {{-- Partner Height From --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Height From
                        </label>

                        <select name="partner_height_from" class="form-select">
                            <option value="">Select Height</option>
                            @foreach($heights as $height)
                            <option value="{{ $height->height_value ?? $height->height }}" @selected(old('partner_height_from', $member->partner_height_from) == ($height->height_value ?? $height->height))>{{ \App\Support\HeightFormatter::format($height->height) }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Height To
                        </label>

                        <select name="partner_height_to" class="form-select">
                            <option value="">Select Height</option>
                            @foreach($heights as $height)
                            <option value="{{ $height->height_value ?? $height->height }}" @selected(old('partner_height_to', $member->partner_height_to) == ($height->height_value ?? $height->height))>{{ \App\Support\HeightFormatter::format($height->height) }}</option>
                            @endforeach
                        </select>

                    </div>

                </div>


                {{-- =====================================================
            LOCATION
        ====================================================== --}}

                <h6 class="text-primary border-bottom pb-2 mb-3 mt-5">

                    <i class="bi bi-geo-alt me-2"></i>
                    Location Preference

                </h6>


                <div class="row g-3">


                    {{-- Country --}}
                    <div class="col-md-4">

                        <label for="partner_country" class="form-label">
                            Country
                        </label>

                        <select name="partner_country" id="partner_country" class="form-select">
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                            <option value="{{ $country->name }}" data-id="{{ $country->id }}" @selected(old('partner_country', $member->partner_country) == $country->name)>{{ $country->name }}</option>
                            @endforeach
                        </select>

                    </div>


                    {{-- State --}}
                    <div class="col-md-4">

                        <label for="partner_state" class="form-label">
                            State
                        </label>

                        <select name="partner_state" id="partner_state" class="form-select" data-current="{{ old('partner_state', $member->partner_state) }}">
                            @if(old('partner_state', $member->partner_state))
                                <option selected value="{{ old('partner_state', $member->partner_state) }}">{{ old('partner_state', $member->partner_state) }}</option>
                            @endif
                            <option value="">Select Country First</option>
                        </select>

                    </div>


                    {{-- City --}}
                    <div class="col-md-4">

                        <label for="partner_city" class="form-label">
                            City
                        </label>

                        <select name="partner_city" id="partner_city" class="form-select" data-current="{{ old('partner_city', $member->partner_city) }}">
                            @if(old('partner_city', $member->partner_city))
                                <option selected value="{{ old('partner_city', $member->partner_city) }}">{{ old('partner_city', $member->partner_city) }}</option>
                            @endif
                            <option value="">Select State First</option>
                        </select>

                    </div>

                </div>


                {{-- =====================================================
            RELIGION & COMMUNITY
        ====================================================== --}}

                <h6 class="text-primary border-bottom pb-2 mb-3 mt-5">

                    <i class="bi bi-stars me-2"></i>
                    Religion & Community

                </h6>


                <div class="row g-3">


                    {{-- Religion --}}
                    <div class="col-md-6">

                        <label for="partner_religion" class="form-label">
                            Religion
                        </label>

                        <select name="partner_religion" id="partner_religion" class="form-select">
                            <option value="">Select Religion</option>
                            @foreach($religions as $religion)
                            <option value="{{ $religion->religion }}" @selected(old('partner_religion', $member->partner_religion) == $religion->religion)>{{ $religion->religion }}</option>
                            @endforeach
                        </select>

                    </div>


                    {{-- Caste --}}
                    <div class="col-md-6">

                        <label for="partner_cast" class="form-label">
                            Caste
                        </label>

                        <select name="partner_cast" id="partner_cast" class="form-select">
                            <option value="">Select Caste</option>
                            @foreach($casts as $cast)
                            <option value="{{ $cast->cast }}" @selected(old('partner_cast', $member->partner_cast) == $cast->cast)>{{ $cast->cast }}</option>
                            @endforeach
                        </select>

                    </div>


                    {{-- Mother Tongue --}}
                    <div class="col-md-6">

                        <label for="partner_mothertongue" class="form-label">
                            Mother Tongue
                        </label>

                        <select name="partner_mothertongue" id="partner_mothertongue" class="form-select">
                            <option value="">Select Mother Tongue</option>
                            @if(old('partner_mothertongue', $member->partner_mothertongue) && ! $motherTongues->contains('mother_tongue', old('partner_mothertongue', $member->partner_mothertongue)))
                                <option selected value="{{ old('partner_mothertongue', $member->partner_mothertongue) }}">{{ old('partner_mothertongue', $member->partner_mothertongue) }}</option>
                            @endif
                            @foreach($motherTongues as $motherTongue)
                            <option value="{{ $motherTongue->mother_tongue }}" @selected(old('partner_mothertongue', $member->partner_mothertongue) == $motherTongue->mother_tongue)>{{ $motherTongue->mother_tongue }}</option>
                            @endforeach
                        </select>

                    </div>


                    {{-- Manglik --}}
                    <div class="col-md-6">

                        <label for="is_partner_manglik" class="form-label">
                            Manglik Preference
                        </label>

                        <select
                            class="form-select"
                            id="is_partner_manglik"
                            name="is_partner_manglik">

                            <option value="">
                                Select Preference
                            </option>

                            <option
                                value="Yes"
                                {{ old('is_partner_manglik', $member->is_partner_manglik) == 'Yes' ? 'selected' : '' }}>
                                Yes
                            </option>

                            <option
                                value="No"
                                {{ old('is_partner_manglik', $member->is_partner_manglik) == 'No' ? 'selected' : '' }}>
                                No
                            </option>

                        </select>

                    </div>

                </div>


                {{-- =====================================================
            EDUCATION & CAREER
        ====================================================== --}}

                <h6 class="text-primary border-bottom pb-2 mb-3 mt-5">

                    <i class="bi bi-mortarboard me-2"></i>
                    Education & Career

                </h6>


                <div class="row g-3">


                    {{-- Education --}}
                    <div class="col-md-6">

                        <label for="partner_education" class="form-label">
                            Education
                        </label>

                        <select name="partner_education" id="partner_education" class="form-select">
                            <option value="">Select Education</option>
                            @if(old('partner_education', $member->partner_education) && ! $educations->contains('education', old('partner_education', $member->partner_education)))
                                <option selected value="{{ old('partner_education', $member->partner_education) }}">{{ old('partner_education', $member->partner_education) }}</option>
                            @endif
                            @foreach($educations as $education)
                            <option value="{{ $education->education }}" @selected(old('partner_education', $member->partner_education) == $education->education)>{{ $education->education }}</option>
                            @endforeach
                        </select>

                    </div>


                    {{-- Occupation --}}
                    <div class="col-md-6">

                        <label for="partner_occupation" class="form-label">
                            Occupation
                        </label>

                        <select name="partner_occupation" id="partner_occupation" class="form-select">
                            <option value="">Select Occupation</option>
                            @foreach($occupations as $occupation)
                            <option value="{{ $occupation->occupation }}" @selected(old('partner_occupation', $member->partner_occupation) == $occupation->occupation)>{{ $occupation->occupation }}</option>
                            @endforeach
                        </select>

                    </div>


                    {{-- Annual Income From --}}
                    <div class="col-md-6">

                        <label for="partner_annual_income_from" class="form-label">
                            Annual Income From
                        </label>

                        <select name="partner_annual_income_from" id="partner_annual_income_from" class="form-select">
                            <option value="">Select Income</option>
                            @foreach($annualIncomes as $income)
                            <option value="{{ $income->annual_income }}" @selected(old('partner_annual_income_from', $member->partner_annual_income_from) == $income->annual_income)>{{ $income->annual_income }}</option>
                            @endforeach
                        </select>

                    </div>


                    {{-- Annual Income To --}}
                    <div class="col-md-6">

                        <label for="partner_annual_income_to" class="form-label">
                            Annual Income To
                        </label>

                        <select name="partner_annual_income_to" id="partner_annual_income_to" class="form-select">
                            <option value="">Select Income</option>
                            @foreach($annualIncomes as $income)
                            <option value="{{ $income->annual_income }}" @selected(old('partner_annual_income_to', $member->partner_annual_income_to) == $income->annual_income)>{{ $income->annual_income }}</option>
                            @endforeach
                        </select>

                    </div>

                </div>


                {{-- =====================================================
            LIFESTYLE
        ====================================================== --}}

                <h6 class="text-primary border-bottom pb-2 mb-3 mt-5">

                    <i class="bi bi-cup-hot me-2"></i>
                    Lifestyle Preferences

                </h6>


                <div class="row g-3">


                    {{-- Diet --}}
                    <div class="col-md-4">

                        <label for="partner_diet" class="form-label">
                            Diet
                        </label>

                        <select name="partner_diet" id="partner_diet" class="form-select">
                            <option value="">Select Diet</option>
                            @foreach(['Veg', 'Veg & Non Veg', 'Non Veg'] as $value)
                            <option value="{{ $value }}" @selected(old('partner_diet', $member->partner_diet) == $value)>{{ $value }}</option>
                            @endforeach
                        </select>

                    </div>


                    {{-- Smoking --}}
                    <div class="col-md-4">

                        <label for="is_partner_smoking" class="form-label">
                            Smoking
                        </label>

                        <select
                            class="form-select"
                            id="is_partner_smoking"
                            name="is_partner_smoking">

                            <option value="">
                                Select Preference
                            </option>

                            @foreach(['Yes', 'No', 'Occasionally'] as $value)
                            <option value="{{ $value }}" @selected(old('is_partner_smoking', $member->is_partner_smoking) == $value)>{{ $value }}</option>
                            @endforeach

                        </select>

                    </div>


                    {{-- Drinking --}}
                    <div class="col-md-4">

                        <label for="is_partner_drinking" class="form-label">
                            Drinking
                        </label>

                        <select
                            class="form-select"
                            id="is_partner_drinking"
                            name="is_partner_drinking">

                            <option value="">
                                Select Preference
                            </option>

                            @foreach(['Yes', 'No', 'Occasionally'] as $value)
                            <option value="{{ $value }}" @selected(old('is_partner_drinking', $member->is_partner_drinking) == $value)>{{ $value }}</option>
                            @endforeach

                        </select>

                    </div>

                </div>


                {{-- =====================================================
            ABOUT PARTNER
        ====================================================== --}}

                <h6 class="text-primary border-bottom pb-2 mb-3 mt-5">

                    <i class="bi bi-chat-heart me-2"></i>
                    About My Partner

                </h6>


                <div class="row">

                    <div class="col-12">

                        <label for="about_my_partner" class="form-label">
                            Partner Description
                        </label>

                        <textarea
                            class="form-control"
                            id="about_my_partner"
                            name="about_my_partner"
                            rows="5"
                            placeholder="Describe the kind of partner you are looking for...">{{ old('about_my_partner', $member->about_my_partner) }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        @php
            $profileRangeRows = old(
                'profile_ranges',
                $memberProfileRanges->map(fn ($range) => [
                    'range_from' => $range->range_from,
                    'range_to' => $range->range_to,
                    'price' => $range->price,
                ])->all()
            );
            $profileRangeRows = $profileRangeRows ?: [
                ['range_from' => '', 'range_to' => '', 'price' => ''],
            ];
        @endphp

        <div class="card border-0 shadow-sm mb-4 member-section">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-currency-rupee me-2"></i>
                    Profile View Rate
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Set the price charged according to how many profiles this member has viewed.
                </p>
                <div class="row g-2 d-none d-md-flex fw-semibold small text-muted mb-1">
                    <div class="col-md-4">From</div>
                    <div class="col-md-4">To</div>
                    <div class="col-md-4">Price</div>
                </div>

                <div id="profileRangeRows">
                @foreach($profileRangeRows as $index => $range)
                <div class="profile-range-row row g-2 mb-3 align-items-center">
                    <div class="col-md-4">
                        <label class="form-label d-md-none" for="profile_range_from_{{ $index }}">From</label>
                        <input type="number" name="profile_ranges[{{ $index }}][range_from]" id="profile_range_from_{{ $index }}"
                            class="form-control @error('profile_ranges.'.$index.'.range_from') is-invalid @enderror"
                            value="{{ $range['range_from'] ?? '' }}" min="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-md-none" for="profile_range_to_{{ $index }}">To</label>
                        <input type="number" name="profile_ranges[{{ $index }}][range_to]" id="profile_range_to_{{ $index }}"
                            class="form-control @error('profile_ranges.'.$index.'.range_to') is-invalid @enderror"
                            value="{{ $range['range_to'] ?? '' }}" min="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-md-none" for="profile_range_price_{{ $index }}">Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="profile_ranges[{{ $index }}][price]"
                                id="profile_range_price_{{ $index }}"
                                class="form-control @error('profile_ranges.'.$index.'.price') is-invalid @enderror"
                                value="{{ $range['price'] ?? '' }}" min="0" max="1000000" step="0.01">
                        </div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-profile-range">Remove</button>
                    </div>
                </div>
                @endforeach
                </div>

                <button type="button" id="addProfileRange" class="btn btn-sm btn-outline-primary mt-1">
                    <i class="bi bi-plus-lg me-1"></i>Add Range
                </button>

                @error('profile_ranges')
                <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

    </form>

    @include('admin.members.partials.photo-management')

    @include('admin.members.partials.identity-proof')

    {{-- Save Buttons --}}
    <div class="d-flex justify-content-end gap-2 mb-5">
        <a
            href="{{ route('admin.members.show', $member->id) }}"
            class="btn btn-light">
            Cancel
        </a>

        <button
            type="submit"
            form="member-edit-form"
            class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i>
            Save Changes
        </button>
    </div>

</div>

@push('styles')
<style>
    .member-section .card-header {
        padding: 1.5rem 1.5rem 0;
        background: #fff;
        border: 0;
    }

    .member-section .card-header h5 {
        display: flex;
        align-items: center;
        margin-bottom: 0;
        font-size: 1.1rem;
    }

    .member-section .card-header h5>i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        margin-right: 1rem !important;
        border-radius: 50%;
        background: var(--bs-primary-bg-subtle);
        color: var(--bs-primary);
        font-size: 1.15rem;
    }

    .member-section .card-body {
        padding: 1.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('member-edit-form');
        const country = document.getElementById('country_living_in');
        const state = document.getElementById('state_living_in');
        const city = document.getElementById('city_living_in');
        const partnerCountry = document.getElementById('partner_country');
        const partnerState = document.getElementById('partner_state');
        const partnerCity = document.getElementById('partner_city');
        const disability = document.getElementById('any_disability');
        const disabilityGroup = document.getElementById('disability_description_group');
        const disabilityDescription = document.getElementById('health_info');
        const idProofInput = document.getElementById('id_proof');
        const idProofPreview = document.getElementById('idProofPreview');
        const idProofPreviewContainer = document.getElementById('idProofPreviewContainer');
        const idProofEmpty = document.getElementById('idProofEmpty');
        const brothersSelect = document.getElementById('no_of_brothers');
        const marriedBrothersSelect = document.getElementById('married_brothers');
        const marriedBrothersZero = document.getElementById('married_brothers_zero');
        const sistersSelect = document.getElementById('no_of_sisters');
        const marriedSistersSelect = document.getElementById('married_sisters');
        const marriedSistersZero = document.getElementById('married_sisters_zero');

        function syncMarriedSiblingCount(totalSelect, marriedSelect, zeroInput) {
            if (!totalSelect || !marriedSelect || !zeroInput) return;

            const total = Number.parseInt(totalSelect.value, 10);
            const hasTotal = Number.isInteger(total);
            const hasNoSiblings = hasTotal && total === 0;

            marriedSelect.disabled = hasNoSiblings;
            zeroInput.disabled = !hasNoSiblings;
            zeroInput.name = hasNoSiblings ? marriedSelect.name : '';

            Array.from(marriedSelect.options).forEach(option => {
                if (option.value === '') return;
                option.disabled = hasTotal && Number(option.value) > total;
            });

            if (hasNoSiblings) {
                marriedSelect.value = '0';
            } else if (hasTotal && Number(marriedSelect.value) > total) {
                marriedSelect.value = String(total);
            }
        }

        brothersSelect?.addEventListener('change', () =>
            syncMarriedSiblingCount(brothersSelect, marriedBrothersSelect, marriedBrothersZero)
        );
        sistersSelect?.addEventListener('change', () =>
            syncMarriedSiblingCount(sistersSelect, marriedSistersSelect, marriedSistersZero)
        );
        syncMarriedSiblingCount(brothersSelect, marriedBrothersSelect, marriedBrothersZero);
        syncMarriedSiblingCount(sistersSelect, marriedSistersSelect, marriedSistersZero);

        const profileRangeRows = document.getElementById('profileRangeRows');
        const addProfileRangeButton = document.getElementById('addProfileRange');

        function addProfileRangeRow() {
            if (!profileRangeRows || profileRangeRows.children.length >= 20) return;

            const index = Date.now();
            const row = document.createElement('div');
            row.className = 'profile-range-row row g-2 mb-3 align-items-center';
            row.innerHTML = `
                <div class="col-md-4">
                    <label class="form-label d-md-none" for="profile_range_from_${index}">From</label>
                    <input type="number" name="profile_ranges[${index}][range_from]" id="profile_range_from_${index}" class="form-control" min="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label d-md-none" for="profile_range_to_${index}">To</label>
                    <input type="number" name="profile_ranges[${index}][range_to]" id="profile_range_to_${index}" class="form-control" min="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label d-md-none" for="profile_range_price_${index}">Price</label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" name="profile_ranges[${index}][price]" id="profile_range_price_${index}" class="form-control" min="0" max="1000000" step="0.01">
                    </div>
                </div>
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-profile-range">Remove</button>
                </div>`;
            profileRangeRows.appendChild(row);
        }

        addProfileRangeButton?.addEventListener('click', addProfileRangeRow);
        profileRangeRows?.addEventListener('click', event => {
            const removeButton = event.target.closest('.remove-profile-range');
            if (!removeButton) return;

            removeButton.closest('.profile-range-row')?.remove();
            if (!profileRangeRows.children.length) addProfileRangeRow();
        });

        const selectedId = select => select?.selectedOptions?.[0]?.dataset?.id || '';
        const option = item => {
            const element = document.createElement('option');
            element.value = item.name;
            element.textContent = item.name;
            element.dataset.id = item.id;
            return element;
        };

        function setupLocation(countrySelect, stateSelect, citySelect) {
            let stateVersion = 0;
            let cityVersion = 0;

            function populate(select, items, selected, placeholder) {
                select.innerHTML = '';
                select.appendChild(option({ name: '', id: '' }));
                select.options[0].textContent = placeholder;
                items.forEach(item => select.appendChild(option(item)));
                if (selected && !items.some(item => item.name === selected)) {
                    select.appendChild(option({ name: selected, id: '' }));
                }
                select.value = selected;
                select.disabled = false;
                select.setCustomValidity('');
            }

            async function fetchOptions(select, url, selected, isCurrent, placeholder) {
                select.dataset.loading = 'true';
                try {
                    const response = await fetch(url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!response.ok) throw new Error('Unable to load locations');
                    const items = await response.json();
                    if (!isCurrent()) return false;
                    populate(select, items, selected, placeholder);
                    select.setCustomValidity('');
                    return true;
                } catch (error) {
                    if (isCurrent()) {
                        // Retain saved values when the lookup service is unavailable.
                        populate(select, [], selected, 'Unable to load locations; reselect the parent to retry');
                    }
                    return false;
                } finally {
                    if (isCurrent()) delete select.dataset.loading;
                }
            }

            async function loadCities(selected = '') {
                const version = ++cityVersion;
                const id = selectedId(stateSelect);
                populate(citySelect, [], selected, 'Select City');
                if (!id) {
                    delete citySelect.dataset.loading;
                    return;
                }
                await fetchOptions(citySelect,
                    form.dataset.citiesUrl.replace('__ID__', encodeURIComponent(id)),
                    selected, () => version === cityVersion, 'Select City');
            }

            async function loadStates(selectedState = '', selectedCity = '') {
                const version = ++stateVersion;
                ++cityVersion;
                delete citySelect.dataset.loading;
                populate(stateSelect, [], selectedState, 'Select State');
                populate(citySelect, [], selectedCity, 'Select City');
                const id = selectedId(countrySelect);
                if (!id) {
                    delete stateSelect.dataset.loading;
                    return;
                }
                const loaded = await fetchOptions(stateSelect,
                    form.dataset.statesUrl.replace('__ID__', encodeURIComponent(id)),
                    selectedState, () => version === stateVersion, 'Select State');
                if (loaded && selectedState) await loadCities(selectedCity);
            }

            countrySelect.addEventListener('change', () => loadStates());
            stateSelect.addEventListener('change', () => loadCities());
            if (countrySelect.value) loadStates(stateSelect.dataset.current, citySelect.dataset.current);
        }

        setupLocation(country, state, city);
        setupLocation(partnerCountry, partnerState, partnerCity);

        form.addEventListener('submit', event => {
            const loading = [state, city, partnerState, partnerCity].find(select => select.dataset.loading);
            if (loading) {
                event.preventDefault();
                loading.setCustomValidity('Please wait for locations to finish loading.');
                loading.reportValidity();
            }
        });

        function toggleDisabilityDescription(clearWhenHidden = false) {
            const visible = disability?.value === 'Yes';
            disabilityGroup?.classList.toggle('d-none', !visible);
            if (disabilityDescription) {
                disabilityDescription.required = visible;
                if (!visible && clearWhenHidden) disabilityDescription.value = '';
            }
        }

        disability?.addEventListener('change', () => toggleDisabilityDescription(true));
        toggleDisabilityDescription();

        idProofInput?.addEventListener('change', function() {
            const file = this.files?.[0];
            if (!file) return;

            idProofPreview.src = URL.createObjectURL(file);
            idProofPreviewContainer?.classList.remove('d-none');
            idProofEmpty?.classList.add('d-none');
        });
    });
</script>
@endpush

@endsection
