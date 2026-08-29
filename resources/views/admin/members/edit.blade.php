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
        action="{{ route('admin.members.update', $member->id) }}"
        method="POST">

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

                        <label class="form-label">
                            Birth Date
                        </label>

                        <input
                            type="text"
                            name="birth_date_time"
                            class="form-control"
                            value="{{ old('birth_date_time', $member->birth_date_time) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Height
                        </label>

                        <input
                            type="text"
                            name="height"
                            class="form-control"
                            placeholder="5ft 10in"
                            value="{{ old('height', $member->height) }}">

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

                        <input
                            type="text"
                            name="marital_status"
                            class="form-control"
                            value="{{ old('marital_status', $member->marital_status) }}">

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

                        <input
                            type="text"
                            name="religion"
                            class="form-control"
                            value="{{ old('religion', $member->religion) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Mother Tongue
                        </label>

                        <input
                            type="text"
                            name="mother_tongue"
                            class="form-control"
                            value="{{ old('mother_tongue', $member->mother_tongue) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Caste
                        </label>

                        <input
                            type="text"
                            name="cast"
                            class="form-control"
                            value="{{ old('cast', $member->cast) }}">

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

                        <input
                            type="text"
                            name="manglik"
                            class="form-control"
                            value="{{ old('manglik', $member->manglik) }}">

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

                        <input
                            type="text"
                            name="education"
                            class="form-control"
                            value="{{ old('education', $member->education) }}">

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

                        <input
                            type="text"
                            name="employed_in"
                            class="form-control"
                            value="{{ old('employed_in', $member->employed_in) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Occupation
                        </label>

                        <input
                            type="text"
                            name="occupation"
                            class="form-control"
                            value="{{ old('occupation', $member->occupation) }}">

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

                        <input
                            type="text"
                            name="annual_income"
                            class="form-control"
                            value="{{ old('annual_income', $member->annual_income) }}">

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

                        <input
                            type="text"
                            name="country_living_in"
                            class="form-control"
                            value="{{ old('country_living_in', $member->country_living_in) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            State
                        </label>

                        <input
                            type="text"
                            name="state_living_in"
                            class="form-control"
                            value="{{ old('state_living_in', $member->state_living_in) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            City
                        </label>

                        <input
                            type="text"
                            name="city_living_in"
                            class="form-control"
                            value="{{ old('city_living_in', $member->city_living_in) }}">

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

                    <div class="col-md-4">

                        <label class="form-label">
                            Family Type
                        </label>

                        <input
                            type="text"
                            name="family_type"
                            class="form-control"
                            value="{{ old('family_type', $member->family_type) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Family Status
                        </label>

                        <input
                            type="text"
                            name="family_status"
                            class="form-control"
                            value="{{ old('family_status', $member->family_status) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Family Income
                        </label>

                        <input
                            type="text"
                            name="family_income"
                            class="form-control"
                            value="{{ old('family_income', $member->family_income) }}">

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

                        <input
                            type="text"
                            name="diet"
                            class="form-control"
                            value="{{ old('diet', $member->diet) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Drinking
                        </label>

                        <input
                            type="text"
                            name="is_drinking"
                            class="form-control"
                            value="{{ old('is_drinking', $member->is_drinking) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Smoking
                        </label>

                        <input
                            type="text"
                            name="is_smoking"
                            class="form-control"
                            value="{{ old('is_smoking', $member->is_smoking) }}">

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Disability
                        </label>

                        <input
                            type="text"
                            name="any_disability"
                            class="form-control"
                            value="{{ old('any_disability', $member->any_disability) }}">

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

                        <input
                            type="text"
                            name="relationship_manager"
                            class="form-control"
                            value="{{ old('relationship_manager', $member->relationship_manager) }}">

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

                        <input
                            type="text"
                            class="form-control"
                            id="looking_for"
                            name="looking_for"
                            value="{{ old('looking_for', $member->looking_for) }}"
                            placeholder="Example: Marriage">

                    </div>


                    {{-- Partner Age From --}}
                    <div class="col-md-3">

                        <label for="partner_age_from" class="form-label">
                            Age From
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="partner_age_from"
                            name="partner_age_from"
                            value="{{ old('partner_age_from', $member->partner_age_from) }}"
                            placeholder="Example: 25">

                    </div>


                    {{-- Partner Age To --}}
                    <div class="col-md-3">

                        <label for="partner_age_to" class="form-label">
                            Age To
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="partner_age_to"
                            name="partner_age_to"
                            value="{{ old('partner_age_to', $member->partner_age_to) }}"
                            placeholder="Example: 30">

                    </div>


                    {{-- Partner Height From --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Height From
                        </label>

                        <div class="row g-2">

                            <div class="col-6">

                                <select
                                    name="partner_height_from_feet"
                                    class="form-select">

                                    <option value="">Feet</option>

                                    @for($feet = 4; $feet <= 7; $feet++)

                                        <option value="{{ $feet }}">
                                        {{ $feet }} ft
                                        </option>

                                        @endfor

                                </select>

                            </div>


                            <div class="col-6">

                                <select
                                    name="partner_height_from_inches"
                                    class="form-select">

                                    <option value="">Inches</option>

                                    @for($inch = 0; $inch <= 11; $inch++)

                                        <option value="{{ $inch }}">
                                        {{ $inch }} in
                                        </option>

                                        @endfor

                                </select>

                            </div>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Height To
                        </label>

                        <div class="row g-2">

                            <div class="col-6">

                                <select
                                    name="partner_height_to_feet"
                                    class="form-select">

                                    <option value="">Feet</option>

                                    @for($feet = 4; $feet <= 7; $feet++)

                                        <option value="{{ $feet }}">
                                        {{ $feet }} ft
                                        </option>

                                        @endfor

                                </select>

                            </div>


                            <div class="col-6">

                                <select
                                    name="partner_height_to_inches"
                                    class="form-select">

                                    <option value="">Inches</option>

                                    @for($inch = 0; $inch <= 11; $inch++)

                                        <option value="{{ $inch }}">
                                        {{ $inch }} in
                                        </option>

                                        @endfor

                                </select>

                            </div>

                        </div>

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

                        <input
                            type="text"
                            class="form-control"
                            id="partner_country"
                            name="partner_country"
                            value="{{ old('partner_country', $member->partner_country) }}"
                            placeholder="Country">

                    </div>


                    {{-- State --}}
                    <div class="col-md-4">

                        <label for="partner_state" class="form-label">
                            State
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="partner_state"
                            name="partner_state"
                            value="{{ old('partner_state', $member->partner_state) }}"
                            placeholder="State">

                    </div>


                    {{-- City --}}
                    <div class="col-md-4">

                        <label for="partner_city" class="form-label">
                            City
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="partner_city"
                            name="partner_city"
                            value="{{ old('partner_city', $member->partner_city) }}"
                            placeholder="City">

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

                        <input
                            type="text"
                            class="form-control"
                            id="partner_religion"
                            name="partner_religion"
                            value="{{ old('partner_religion', $member->partner_religion) }}"
                            placeholder="Religion">

                    </div>


                    {{-- Caste --}}
                    <div class="col-md-6">

                        <label for="partner_cast" class="form-label">
                            Caste
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="partner_cast"
                            name="partner_cast"
                            value="{{ old('partner_cast', $member->partner_cast) }}"
                            placeholder="Caste">

                    </div>


                    {{-- Mother Tongue --}}
                    <div class="col-md-6">

                        <label for="partner_mothertongue" class="form-label">
                            Mother Tongue
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="partner_mothertongue"
                            name="partner_mothertongue"
                            value="{{ old('partner_mothertongue', $member->partner_mothertongue) }}"
                            placeholder="Mother Tongue">

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

                            <option
                                value="Doesn't Matter"
                                {{ old('is_partner_manglik', $member->is_partner_manglik) == "Doesn't Matter" ? 'selected' : '' }}>
                                Doesn't Matter
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

                        <input
                            type="text"
                            class="form-control"
                            id="partner_education"
                            name="partner_education"
                            value="{{ old('partner_education', $member->partner_education) }}"
                            placeholder="Education">

                    </div>


                    {{-- Occupation --}}
                    <div class="col-md-6">

                        <label for="partner_occupation" class="form-label">
                            Occupation
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="partner_occupation"
                            name="partner_occupation"
                            value="{{ old('partner_occupation', $member->partner_occupation) }}"
                            placeholder="Occupation">

                    </div>


                    {{-- Annual Income From --}}
                    <div class="col-md-6">

                        <label for="partner_annual_income_from" class="form-label">
                            Annual Income From
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="partner_annual_income_from"
                            name="partner_annual_income_from"
                            value="{{ old('partner_annual_income_from', $member->partner_annual_income_from) }}"
                            placeholder="Example: ₹5 Lakh">

                    </div>


                    {{-- Annual Income To --}}
                    <div class="col-md-6">

                        <label for="partner_annual_income_to" class="form-label">
                            Annual Income To
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="partner_annual_income_to"
                            name="partner_annual_income_to"
                            value="{{ old('partner_annual_income_to', $member->partner_annual_income_to) }}"
                            placeholder="Example: ₹15 Lakh">

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

                        <input
                            type="text"
                            class="form-control"
                            id="partner_diet"
                            name="partner_diet"
                            value="{{ old('partner_diet', $member->partner_diet) }}"
                            placeholder="Diet">

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

                            <option
                                value="Yes"
                                {{ old('is_partner_smoking', $member->is_partner_smoking) == 'Yes' ? 'selected' : '' }}>
                                Yes
                            </option>

                            <option
                                value="No"
                                {{ old('is_partner_smoking', $member->is_partner_smoking) == 'No' ? 'selected' : '' }}>
                                No
                            </option>

                            <option
                                value="Doesn't Matter"
                                {{ old('is_partner_smoking', $member->is_partner_smoking) == "Doesn't Matter" ? 'selected' : '' }}>
                                Doesn't Matter
                            </option>

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

                            <option
                                value="Yes"
                                {{ old('is_partner_drinking', $member->is_partner_drinking) == 'Yes' ? 'selected' : '' }}>
                                Yes
                            </option>

                            <option
                                value="No"
                                {{ old('is_partner_drinking', $member->is_partner_drinking) == 'No' ? 'selected' : '' }}>
                                No
                            </option>

                            <option
                                value="Doesn't Matter"
                                {{ old('is_partner_drinking', $member->is_partner_drinking) == "Doesn't Matter" ? 'selected' : '' }}>
                                Doesn't Matter
                            </option>

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


        {{-- Save Buttons --}}

        <div class="d-flex justify-content-end gap-2 mb-5">

            <a
                href="{{ route('admin.members.show', $member->id) }}"
                class="btn btn-light">

                Cancel

            </a>

            <button
                type="submit"
                class="btn btn-primary">

                <i class="bi bi-check-lg me-1"></i>
                Save Changes

            </button>

        </div>

    </form>

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

    .member-section .card-header h5 > i {
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

@endsection
