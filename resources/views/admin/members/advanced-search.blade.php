@extends('admin.layout')
@section('content')

<div class="container-fluid py-4">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">
                Advanced Member Search
            </h1>

            <p class="text-muted mb-0">
                Search members using detailed profile information.
            </p>
        </div>

        <a href="{{ route('admin.members.index') }}"
            class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Members
        </a>

    </div>


    {{-- =========================================================
        SEARCH FORM
    ========================================================== --}}
    <form
        method="GET"
        action="{{ route('admin.members.advanced-search.results') }}"
        id="advancedSearchForm">

        {{-- =====================================================
            BASIC INFORMATION
        ====================================================== --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <h5 class="mb-0">
                    Basic Information
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Profile ID --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Profile ID
                        </label>

                        <input
                            type="text"
                            name="profile_id"
                            class="form-control"
                            value="{{ request('profile_id') }}"
                            placeholder="e.g. HIM27807">

                    </div>


                    {{-- Full Name --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Full Name
                        </label>

                        <input
                            type="text"
                            name="full_name"
                            class="form-control"
                            value="{{ request('full_name') }}"
                            placeholder="Enter member name">

                    </div>


                    {{-- Email --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ request('email') }}"
                            placeholder="Enter email">

                    </div>


                    {{-- Mobile --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Mobile Number
                        </label>

                        <input
                            type="text"
                            name="mobile_number"
                            class="form-control"
                            value="{{ request('mobile_number') }}"
                            placeholder="Enter mobile number">

                    </div>


                    {{-- Profile Created For --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Profile Created For
                        </label>

                        <select
                            name="profile_created_for"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Self"
                                @selected(request('profile_created_for')==='Self' )>
                                Self
                            </option>

                            <option value="Son"
                                @selected(request('profile_created_for')==='Son' )>
                                Son
                            </option>

                            <option value="Daughter"
                                @selected(request('profile_created_for')==='Daughter' )>
                                Daughter
                            </option>

                            <option value="Brother"
                                @selected(request('profile_created_for')==='Brother' )>
                                Brother
                            </option>

                            <option value="Sister"
                                @selected(request('profile_created_for')==='Sister' )>
                                Sister
                            </option>

                            <option value="Relative"
                                @selected(request('profile_created_for')==='Relative' )>
                                Relative
                            </option>

                        </select>

                    </div>


                    {{-- Gender --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Gender
                        </label>

                        <select
                            name="gender"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Male"
                                @selected(request('gender')==='Male' )>
                                Male
                            </option>

                            <option value="Female"
                                @selected(request('gender')==='Female' )>
                                Female
                            </option>

                        </select>

                    </div>


                    {{-- Age From --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Age From
                        </label>

                        <input
                            type="number"
                            name="age_from"
                            class="form-control"
                            min="18"
                            max="100"
                            value="{{ request('age_from') }}"
                            placeholder="Minimum age">

                    </div>


                    {{-- Age To --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Age To
                        </label>

                        <input
                            type="number"
                            name="age_to"
                            class="form-control"
                            min="18"
                            max="100"
                            value="{{ request('age_to') }}"
                            placeholder="Maximum age">

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            PERSONAL INFORMATION
        ========================================================== --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <h5 class="mb-0">
                    Personal Information
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Religion --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Religion
                        </label>

                        <select
                            name="religion"
                            class="form-select">

                            <option value="">
                                All Religions
                            </option>

                            @foreach($religions as $religion)

                            <option
                                value="{{ $religion->religion }}"
                                @selected(
                                request('religion')==$religion->religion
                                )
                                >
                                {{ $religion->religion }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Mother Tongue --}}
                    <div class="col-md-4">

                        <label for="mother_tongue" class="form-label">
                            Mother Tongue
                        </label>

                        <select
                            id="mother_tongue"
                            name="mother_tongue"
                            class="form-select">
                            <option value="">
                                All Mother Tongues
                            </option>

                            @foreach ($motherTongues as $motherTongue)
                            <option
                                value="{{ $motherTongue->mother_tongue }}"
                                {{ request('mother_tongue') == $motherTongue->mother_tongue ? 'selected' : '' }}>
                                {{ $motherTongue->mother_tongue }}
                            </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- Cast --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Cast
                        </label>

                        <select
                            name="cast"
                            class="form-select">

                            <option value="">
                                All Casts
                            </option>

                            @foreach($casts as $cast)

                            <option
                                value="{{ $cast->cast }}"
                                @selected(
                                request('cast')==$cast->cast
                                )
                                >
                                {{ $cast->cast }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Marital Status --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Marital Status
                        </label>

                        <select
                            name="marital_status"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            @foreach($maritalStatuses as $maritalStatus)

                            <option
                                value="{{ $maritalStatus->marital_status }}"
                                @selected(
                                request('marital_status')==$maritalStatus->marital_status
                                )
                                >
                                {{ $maritalStatus->marital_status }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Manglik --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Manglik
                        </label>

                        <select
                            name="manglik"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Yes"
                                @selected(request('manglik')==='Yes' )>
                                Yes
                            </option>

                            <option value="No"
                                @selected(request('manglik')==='No' )>
                                No
                            </option>

                        </select>

                    </div>


                    {{-- Number of Children --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Number of Children
                        </label>

                        <input
                            type="number"
                            name="no_of_child"
                            class="form-control"
                            min="0"
                            value="{{ request('no_of_child') }}"
                            placeholder="Number of children">

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            EDUCATION & CAREER
        ========================================================== --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <h5 class="mb-0">
                    Education & Career
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Education --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Education
                        </label>

                        <select
                            name="education"
                            class="form-select">

                            <option value="">
                                All Educations
                            </option>

                            @foreach($educations as $education)

                            <option
                                value="{{ $education->education }}"
                                @selected(
                                request('education')==$education->education
                                )
                                >
                                {{ $education->education }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Occupation --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Occupation
                        </label>

                        <select
                            name="occupation"
                            class="form-select">

                            <option value="">
                                All Occupations
                            </option>

                            @foreach($occupations as $occupation)

                            <option
                                value="{{ $occupation->occupation }}"
                                @selected(
                                request('occupation')==$occupation->occupation
                                )
                                >
                                {{ $occupation->occupation }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Employed In --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Employed In
                        </label>

                        <select
                            name="employed_in"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Private"
                                @selected(request('employed_in')==='Private' )>
                                Private
                            </option>

                            <option value="Government"
                                @selected(request('employed_in')==='Government' )>
                                Government
                            </option>

                            <option value="Business"
                                @selected(request('employed_in')==='Business' )>
                                Business
                            </option>

                            <option value="Self Employed"
                                @selected(request('employed_in')==='Self Employed' )>
                                Self Employed
                            </option>

                        </select>

                    </div>


                    {{-- Designation --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Designation
                        </label>

                        <input
                            type="text"
                            name="designation"
                            class="form-control"
                            value="{{ request('designation') }}"
                            placeholder="Designation">

                    </div>


                    {{-- Organization --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Organization
                        </label>

                        <input
                            type="text"
                            name="organization_name"
                            class="form-control"
                            value="{{ request('organization_name') }}"
                            placeholder="Organization name">

                    </div>


                    {{-- Job Location --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Job Location
                        </label>

                        <input
                            type="text"
                            name="job_location"
                            class="form-control"
                            value="{{ request('job_location') }}"
                            placeholder="Job location">

                    </div>


                    {{-- Annual Income From --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Annual Income From
                        </label>

                        <input
                            type="number"
                            name="annual_income_from"
                            class="form-control"
                            value="{{ request('annual_income_from') }}"
                            placeholder="Minimum income">

                    </div>


                    {{-- Annual Income To --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Annual Income To
                        </label>

                        <input
                            type="number"
                            name="annual_income_to"
                            class="form-control"
                            value="{{ request('annual_income_to') }}"
                            placeholder="Maximum income">

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            LOCATION
        ========================================================== --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <h5 class="mb-0">
                    Current Location
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Country --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Country
                        </label>

                        <select
                            name="country_living_in"
                            id="search_country_living_in"
                            class="form-select">

                            <option value="">
                                All Countries
                            </option>

                            @foreach($countries as $country)

                            <option
                                value="{{ $country->country }}"
                                data-id="{{ $country->id }}"
                                @selected(
                                request('country_living_in')==$country->country
                                )
                                >
                                {{ $country->country }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- State --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            State
                        </label>

                        <select
                            name="state_living_in"
                            id="search_state_living_in"
                            class="form-select"
                            disabled>

                            <option value="">
                                Select Country First
                            </option>

                        </select>

                    </div>


                    {{-- City --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            City
                        </label>

                        <select
                            name="city_living_in"
                            id="search_city_living_in"
                            class="form-select"
                            disabled>

                            <option value="">
                                Select State First
                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            FAMILY
        ========================================================== --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <h5 class="mb-0">
                    Family Information
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Family Type --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Family Type
                        </label>

                        <select
                            name="family_type"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Joint"
                                @selected(request('family_type')==='Joint' )>
                                Joint
                            </option>

                            <option value="Nuclear"
                                @selected(request('family_type')==='Nuclear' )>
                                Nuclear
                            </option>

                        </select>

                    </div>


                    {{-- Family Status --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Family Status
                        </label>

                        <select
                            name="family_status"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Middle Class"
                                @selected(request('family_status')==='Middle Class' )>
                                Middle Class
                            </option>

                            <option value="Upper Middle Class"
                                @selected(request('family_status')==='Upper Middle Class' )>
                                Upper Middle Class
                            </option>

                            <option value="Rich"
                                @selected(request('family_status')==='Rich' )>
                                Rich
                            </option>

                        </select>

                    </div>


                    {{-- Father Occupation --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Father Occupation
                        </label>

                        <input
                            type="text"
                            name="father_occupation"
                            class="form-control"
                            value="{{ request('father_occupation') }}"
                            placeholder="Father occupation">

                    </div>


                    {{-- Mother Occupation --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Mother Occupation
                        </label>

                        <input
                            type="text"
                            name="mother_occupation"
                            class="form-control"
                            value="{{ request('mother_occupation') }}"
                            placeholder="Mother occupation">

                    </div>


                    {{-- Family Income --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Family Income
                        </label>

                        <input
                            type="text"
                            name="family_income"
                            class="form-control"
                            value="{{ request('family_income') }}"
                            placeholder="Family income">

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            LIFESTYLE
        ========================================================== --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <h5 class="mb-0">
                    Lifestyle
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Diet --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Diet
                        </label>

                        <select
                            name="diet"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Vegetarian"
                                @selected(request('diet')==='Vegetarian' )>
                                Vegetarian
                            </option>

                            <option value="Non Vegetarian"
                                @selected(request('diet')==='Non Vegetarian' )>
                                Non Vegetarian
                            </option>

                            <option value="Eggetarian"
                                @selected(request('diet')==='Eggetarian' )>
                                Eggetarian
                            </option>

                        </select>

                    </div>


                    {{-- Drinking --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Drinking
                        </label>

                        <select
                            name="is_drinking"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Yes"
                                @selected(request('is_drinking')==='Yes' )>
                                Yes
                            </option>

                            <option value="No"
                                @selected(request('is_drinking')==='No' )>
                                No
                            </option>

                        </select>

                    </div>


                    {{-- Smoking --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Smoking
                        </label>

                        <select
                            name="is_smoking"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Yes"
                                @selected(request('is_smoking')==='Yes' )>
                                Yes
                            </option>

                            <option value="No"
                                @selected(request('is_smoking')==='No' )>
                                No
                            </option>

                        </select>

                    </div>


                    {{-- Disability --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Disability
                        </label>

                        <select
                            name="any_disability"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Yes"
                                @selected(request('any_disability')==='Yes' )>
                                Yes
                            </option>

                            <option value="No"
                                @selected(request('any_disability')==='No' )>
                                No
                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            PARTNER PREFERENCES
        ========================================================== --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <h5 class="mb-0">
                    Partner Preferences
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Partner Age From --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Partner Age From
                        </label>

                        <input
                            type="number"
                            name="partner_age_from"
                            class="form-control"
                            min="18"
                            max="100"
                            value="{{ request('partner_age_from') }}"
                            placeholder="Minimum">

                    </div>


                    {{-- Partner Age To --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Partner Age To
                        </label>

                        <input
                            type="number"
                            name="partner_age_to"
                            class="form-control"
                            min="18"
                            max="100"
                            value="{{ request('partner_age_to') }}"
                            placeholder="Maximum">

                    </div>


                    {{-- Partner Country --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Partner Country
                        </label>

                        <select
                            name="partner_country"
                            id="search_partner_country"
                            class="form-select">

                            <option value="">
                                All Countries
                            </option>

                            @foreach($countries as $country)

                            <option
                                value="{{ $country->country }}"
                                data-id="{{ $country->id }}"
                                @selected(
                                request('partner_country')==$country->country
                                )
                                >
                                {{ $country->country }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Partner Religion --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Partner Religion
                        </label>

                        <select
                            name="partner_religion"
                            class="form-select">

                            <option value="">
                                All Religions
                            </option>

                            @foreach($religions as $religion)

                            <option
                                value="{{ $religion->religion }}"
                                @selected(
                                request('partner_religion')==$religion->religion
                                )
                                >
                                {{ $religion->religion }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Partner Cast --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Partner Cast
                        </label>

                        <select
                            name="partner_cast"
                            class="form-select">

                            <option value="">
                                All Casts
                            </option>

                            @foreach($casts as $cast)

                            <option
                                value="{{ $cast->cast }}"
                                @selected(
                                request('partner_cast')==$cast->cast
                                )
                                >
                                {{ $cast->cast }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Partner Education --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Partner Education
                        </label>

                        <select
                            name="partner_education"
                            class="form-select">

                            <option value="">
                                All Educations
                            </option>

                            @foreach($educations as $education)

                            <option
                                value="{{ $education->education }}"
                                @selected(
                                request('partner_education')==$education->education
                                )
                                >
                                {{ $education->education }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Partner Mother Tongue --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Partner Mother Tongue
                        </label>

                        <select
                            name="partner_mothertongue"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            @foreach($motherTongues as $motherTongue)

                            <option
                                value="{{ $motherTongue->mother_tongue }}"
                                @selected(
                                request('partner_mothertongue')==$motherTongue->mother_tongue
                                )
                                >
                                {{ $motherTongue->mother_tongue }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Partner Occupation --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Partner Occupation
                        </label>

                        <select
                            name="partner_occupation"
                            class="form-select">

                            <option value="">
                                All Occupations
                            </option>

                            @foreach($occupations as $occupation)

                            <option
                                value="{{ $occupation->occupation }}"
                                @selected(
                                request('partner_occupation')==$occupation->occupation
                                )
                                >
                                {{ $occupation->occupation }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Partner State --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Partner State
                        </label>

                        <select
                            name="partner_state"
                            id="search_partner_state"
                            class="form-select"
                            disabled>

                            <option value="">
                                Select Country First
                            </option>

                        </select>

                    </div>


                    {{-- Partner City --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Partner City
                        </label>

                        <select
                            name="partner_city"
                            id="search_partner_city"
                            class="form-select"
                            disabled>

                            <option value="">
                                Select State First
                            </option>

                        </select>

                    </div>


                    {{-- Partner Diet --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Partner Diet
                        </label>

                        <select
                            name="partner_diet"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Vegetarian"
                                @selected(request('partner_diet')==='Vegetarian' )>
                                Vegetarian
                            </option>

                            <option value="Non Vegetarian"
                                @selected(request('partner_diet')==='Non Vegetarian' )>
                                Non Vegetarian
                            </option>

                            <option value="Eggetarian"
                                @selected(request('partner_diet')==='Eggetarian' )>
                                Eggetarian
                            </option>

                        </select>

                    </div>


                    {{-- Partner Smoking --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Partner Smoking
                        </label>

                        <select
                            name="is_partner_smoking"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Yes"
                                @selected(request('is_partner_smoking')==='Yes' )>
                                Yes
                            </option>

                            <option value="No"
                                @selected(request('is_partner_smoking')==='No' )>
                                No
                            </option>

                        </select>

                    </div>


                    {{-- Partner Drinking --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Partner Drinking
                        </label>

                        <select
                            name="is_partner_drinking"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Yes"
                                @selected(request('is_partner_drinking')==='Yes' )>
                                Yes
                            </option>

                            <option value="No"
                                @selected(request('is_partner_drinking')==='No' )>
                                No
                            </option>

                        </select>

                    </div>


                    {{-- Partner Manglik --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Partner Manglik
                        </label>

                        <select
                            name="is_partner_manglik"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Yes"
                                @selected(request('is_partner_manglik')==='Yes' )>
                                Yes
                            </option>

                            <option value="No"
                                @selected(request('is_partner_manglik')==='No' )>
                                No
                            </option>

                        </select>

                    </div>


                    {{-- Partner Height From --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Partner Height From
                        </label>

                        <input
                            type="text"
                            name="partner_height_from"
                            class="form-control"
                            value="{{ request('partner_height_from') }}"
                            placeholder="e.g. 5ft 4in">

                    </div>


                    {{-- Partner Height To --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Partner Height To
                        </label>

                        <input
                            type="text"
                            name="partner_height_to"
                            class="form-control"
                            value="{{ request('partner_height_to') }}"
                            placeholder="e.g. 6ft">

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            ACCOUNT / MEMBERSHIP
        ========================================================== --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <h5 class="mb-0">
                    Account & Membership
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Active --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Account Status
                        </label>

                        <select
                            name="active"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Yes"
                                @selected(request('active')==='Yes' )>
                                Active
                            </option>

                            <option value="No"
                                @selected(request('active')==='No' )>
                                Inactive
                            </option>

                        </select>

                    </div>


                    {{-- Member Type --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Membership Type
                        </label>

                        <select
                            name="member_type"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            @foreach($membershipTypes as $membershipType)

                            <option
                                value="{{ $membershipType->name }}"
                                @selected(
                                request('member_type')==$membershipType->name
                                )
                                >
                                {{ $membershipType->name }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Membership Plan --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Membership Plan
                        </label>

                        <select
                            name="plan_id"
                            class="form-select">

                            <option value="">
                                All Plans
                            </option>

                            @foreach($membershipPlans as $membershipPlan)

                            <option
                                value="{{ $membershipPlan->id }}"
                                @selected(
                                request('plan_id')==$membershipPlan->id
                                )
                                >
                                {{ $membershipPlan->name }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Trusted --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Trusted Profile
                        </label>

                        <select
                            name="is_trusted"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Yes"
                                @selected(request('is_trusted')==='Yes' )>
                                Trusted
                            </option>

                            <option value="No"
                                @selected(request('is_trusted')==='No' )>
                                Not Trusted
                            </option>

                        </select>

                    </div>


                    {{-- Promoted --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Promoted
                        </label>

                        <select
                            name="promoted"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="1"
                                @selected(request('promoted')==='1' )>
                                Promoted
                            </option>

                            <option value="0"
                                @selected(request('promoted')==='0' )>
                                Not Promoted
                            </option>

                        </select>

                    </div>


                    {{-- Hidden --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Profile Visibility
                        </label>

                        <select
                            name="profile_hide"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="Yes"
                                @selected(request('profile_hide')==='Yes' )>
                                Hidden
                            </option>

                            <option value="No"
                                @selected(request('profile_hide')==='No' )>
                                Visible
                            </option>

                        </select>

                    </div>


                    {{-- Register Through --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Registered Through
                        </label>

                        <select
                            name="register_through"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="admin"
                                @selected(request('register_through')==='admin' )>
                                Admin
                            </option>

                            <option value="website"
                                @selected(request('register_through')==='website' )>
                                Website
                            </option>

                        </select>

                    </div>


                    {{-- Relationship Manager --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Relationship Manager
                        </label>

                        <input
                            type="text"
                            name="relationship_manager"
                            class="form-control"
                            value="{{ request('relationship_manager') }}"
                            placeholder="Relationship manager">

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            SEARCH BUTTONS
        ========================================================== --}}
        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="{{ route('admin.members.advanced-search') }}"
                        class="btn btn-outline-secondary">
                        Reset
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary px-4">
                        <i class="bi bi-search"></i>
                        Search Members
                    </button>

                </div>

            </div>

        </div>

    </form>


    {{-- =========================================================
        SEARCH RESULTS
        ========================================================== --}}

    @if(isset($members))

    <div class="card shadow-sm">

        <div class="card-header bg-white d-flex justify-content-between align-items-center">

            <div>

                <h5 class="mb-1">
                    Search Results
                </h5>

                <small class="text-muted">
                    {{ $members->total() }} members found
                </small>

            </div>

        </div>


        <div class="card-body p-0">

            @if($members->count())

            {{--
                        IMPORTANT:
                        Replace this section with your existing
                        member listing partial so the table remains
                        exactly the same as the normal Members page.
                    --}}

            @include(
            'admin.members.partials.member-table',
            ['members' => $members]
            )

            @else

            <div class="text-center py-5">

                <div class="mb-3">
                    <i class="bi bi-search fs-1 text-muted"></i>
                </div>

                <h5>
                    No members found
                </h5>

                <p class="text-muted mb-0">
                    Try changing your search criteria.
                </p>

            </div>

            @endif

        </div>

    </div>

    @endif

</div>

@endsection


{{-- =============================================================
     OPTIONAL PAGE CSS
============================================================= --}}

@push('styles')

<style>
    .advanced-search-page .form-label {
        font-weight: 600;
        margin-bottom: 6px;
    }

    .advanced-search-page .card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
    }

    .advanced-search-page .card-header {
        padding: 16px 20px;
    }

    .advanced-search-page .card-body {
        padding: 20px;
    }
</style>

@endpush