@extends('admin.layout')

@vite([
'resources/js/admin/create-member.js',
'resources/css/admin/members/create-member.css'
])

@section('content')

<div class="container-fluid py-4">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">Add Member</h4>

            <p class="text-muted mb-0">
                Create a new member profile.
            </p>
        </div>

        <a href="{{ route('admin.members.index') }}"
            class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Back to Members

        </a>

    </div>


    {{-- =========================================================
        VALIDATION ERRORS
    ========================================================== --}}

    @if ($errors->any())

    <div class="alert alert-danger">

        <div class="fw-semibold mb-2">
            Please correct the following errors:
        </div>

        <ul class="mb-0">

            @foreach ($errors->all() as $error)

            <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

    @endif


    {{-- =========================================================
        SUCCESS
    ========================================================== --}}

    @if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        <i class="bi bi-check-circle me-2"></i>

        {{ session('success') }}

        <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    @endif


    {{-- =========================================================
        FORM
    ========================================================== --}}

    <form
        method="POST"
        action="{{ route('admin.members.store') }}"
        enctype="multipart/form-data">

        @csrf


        {{-- =====================================================
            IMAGES & DOCUMENTS
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">
                    <i class="bi bi-images me-2"></i>
                    Images & Documents
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-4">


                    {{-- =================================================
                        PROFILE PHOTO
                    ================================================== --}}

                    <div class="col-md-6">

                        <div class="upload-card">

                            <label
                                for="photo"
                                class="form-label">

                                Profile Photo

                            </label>


                            <div class="image-upload-wrapper">

                                <div
                                    id="photoPreview"
                                    class="image-preview">

                                    <div class="upload-placeholder">

                                        <span class="upload-icon">
                                            📷
                                        </span>

                                        <span>
                                            Choose profile photo
                                        </span>

                                    </div>

                                </div>


                                <input
                                    type="file"
                                    name="photo"
                                    id="photo"
                                    class="form-control"
                                    accept="image/jpeg,image/png,image/webp">

                            </div>


                            <small class="text-muted">

                                JPG, JPEG, PNG or WEBP.
                                Maximum size: 5 MB.

                            </small>


                            @error('photo')

                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>

                            @enderror

                        </div>

                    </div>


                    {{-- =================================================
                        ID PROOF
                    ================================================== --}}

                    <div class="col-md-6">

                        <div class="upload-card">

                            <label
                                for="id_proof"
                                class="form-label">

                                ID Proof / Document

                            </label>


                            <div class="image-upload-wrapper">

                                <div
                                    id="idProofPreview"
                                    class="image-preview">

                                    <div class="upload-placeholder">

                                        <span class="upload-icon">
                                            📄
                                        </span>

                                        <span>
                                            Choose ID proof
                                        </span>

                                    </div>

                                </div>


                                <input
                                    type="file"
                                    name="id_proof"
                                    id="id_proof"
                                    class="form-control"
                                    accept="image/jpeg,image/png,image/webp">

                            </div>


                            <small class="text-muted">

                                Upload an image of the member's
                                identity document.

                                Maximum size: 5 MB.

                            </small>


                            @error('id_proof')

                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>

                            @enderror

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- =====================================================
            BASIC INFORMATION
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">
                    <i class="bi bi-person me-2"></i>
                    Basic Information
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    {{-- Profile Created For --}}

                    <div class="col-md-4">

                        <label
                            for="profile_created_for"
                            class="form-label">

                            Profile Created For

                            <span class="text-danger">*</span>

                        </label>


                        <select
                            name="profile_created_for"
                            id="profile_created_for"
                            class="form-select"
                            required>

                            <option value="">
                                Select
                            </option>

                            @foreach([
                            'Self',
                            'Son',
                            'Daughter',
                            'Brother',
                            'Sister',
                            'Relative',
                            'Friend'
                            ] as $value)

                            <option
                                value="{{ $value }}"
                                @selected(
                                old('profile_created_for')==$value
                                )>

                                {{ $value }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Full Name --}}

                    <div class="col-md-4">

                        <label
                            for="full_name"
                            class="form-label">

                            Full Name

                            <span class="text-danger">*</span>

                        </label>


                        <input
                            type="text"
                            name="full_name"
                            id="full_name"
                            class="form-control"
                            value="{{ old('full_name') }}"
                            maxlength="255"
                            required>

                    </div>


                    {{-- Gender --}}

                    <div class="col-md-4">

                        <label
                            for="gender"
                            class="form-label">

                            Gender

                            <span class="text-danger">*</span>

                        </label>


                        <select
                            name="gender"
                            id="gender"
                            class="form-select"
                            required>

                            <option value="">
                                Select Gender
                            </option>

                            <option
                                value="Male"
                                @selected(old('gender')=='Male' )>

                                Male

                            </option>

                            <option
                                value="Female"
                                @selected(old('gender')=='Female' )>

                                Female

                            </option>

                        </select>

                    </div>


                    {{-- Email --}}

                    <div class="col-md-4">

                        <label
                            for="email"
                            class="form-label">

                            Email

                            <span class="text-danger">*</span>

                        </label>


                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            maxlength="255"
                            required>

                    </div>


                    {{-- Mobile --}}

                    <div class="col-md-4">

                        <label
                            for="mobile_number"
                            class="form-label">

                            Mobile Number

                            <span class="text-danger">*</span>

                        </label>


                        <input
                            type="text"
                            name="mobile_number"
                            id="mobile_number"
                            class="form-control"
                            value="{{ old('mobile_number') }}"
                            maxlength="255"
                            required>

                    </div>


                    {{-- Alternate Number --}}

                    <div class="col-md-4">

                        <label
                            for="alternate_number"
                            class="form-label">

                            Alternate Number

                        </label>


                        <input
                            type="text"
                            name="alternate_number"
                            id="alternate_number"
                            class="form-control"
                            value="{{ old('alternate_number') }}"
                            maxlength="233">

                    </div>


                    {{-- WhatsApp --}}

                    <div class="col-md-4">

                        <label
                            for="whatsapp_number"
                            class="form-label">

                            WhatsApp Number

                        </label>


                        <input
                            type="text"
                            name="whatsapp_number"
                            id="whatsapp_number"
                            class="form-control"
                            value="{{ old('whatsapp_number') }}"
                            maxlength="50">

                    </div>


                    {{-- Date of Birth --}}

                    <div class="col-md-4">

                        <label
                            for="birth_date_time"
                            class="form-label">

                            Date of Birth

                            <span class="text-danger">*</span>

                        </label>


                        <input
                            type="date"
                            name="birth_date_time"
                            id="birth_date_time"
                            class="form-control"
                            value="{{ old('birth_date_time') }}"
                            required>

                    </div>


                    {{-- Height --}}

                    <div class="col-md-4">

                        <label
                            for="height"
                            class="form-label">

                            Height

                        </label>


                        <select
                            name="height"
                            id="height"
                            class="form-select">

                            <option value="">
                                Select Height
                            </option>

                            @foreach($heights as $height)

                            @php
                            $heightValue =
                            $height->height_value
                            ?? $height->height;
                            @endphp

                            <option
                                value="{{ $heightValue }}"
                                @selected(
                                old('height')==$heightValue
                                )>

                                {{ $height->height }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Blood Group --}}

                    <div class="col-md-4">

                        <label
                            for="blood_group"
                            class="form-label">

                            Blood Group

                        </label>


                        <select
                            name="blood_group"
                            id="blood_group"
                            class="form-select">

                            <option value="">
                                Select Blood Group
                            </option>

                            @foreach([
                            'A+',
                            'A-',
                            'B+',
                            'B-',
                            'AB+',
                            'AB-',
                            'O+',
                            'O-'
                            ] as $blood)

                            <option
                                value="{{ $blood }}"
                                @selected(
                                old('blood_group')==$blood
                                )>

                                {{ $blood }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Health Information --}}

                    <div class="col-md-12">

                        <label
                            for="health_info"
                            class="form-label">

                            Health Information

                        </label>


                        <textarea
                            name="health_info"
                            id="health_info"
                            class="form-control"
                            rows="3">{{ old('health_info') }}</textarea>

                    </div>

                </div>

            </div>

        </div>



        {{-- =====================================================
            RELIGION & HOROSCOPE
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">
                    <i class="bi bi-stars me-2"></i>
                    Religion & Horoscope
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    {{-- Religion --}}

                    <div class="col-md-4">

                        <label
                            for="religion"
                            class="form-label">

                            Religion

                        </label>


                        <select
                            name="religion"
                            id="religion"
                            class="form-select">

                            <option value="">
                                Select Religion
                            </option>

                            @foreach($religions as $religion)

                            <option
                                value="{{ $religion->religion }}"
                                @selected(
                                old('religion')==$religion->religion
                                )>

                                {{ $religion->religion }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Cast --}}

                    <div class="col-md-4">

                        <label
                            for="cast"
                            class="form-label">

                            Cast

                        </label>


                        <select
                            name="cast"
                            id="cast"
                            class="form-select">

                            <option value="">
                                Select Cast
                            </option>

                            @foreach($casts as $cast)

                            <option
                                value="{{ $cast->cast }}"
                                @selected(
                                old('cast')==$cast->cast
                                )>

                                {{ $cast->cast }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Sub Cast --}}

                    <div class="col-md-4">

                        <label
                            for="sub_cast"
                            class="form-label">

                            Sub Cast

                        </label>


                        <input
                            type="text"
                            name="sub_cast"
                            id="sub_cast"
                            class="form-control"
                            value="{{ old('sub_cast') }}">

                    </div>


                    {{-- Mother Tongue --}}

                    <div class="col-md-4">

                        <label
                            for="mother_tongue"
                            class="form-label">

                            Mother Tongue

                        </label>


                        <select
                            name="mother_tongue"
                            id="mother_tongue"
                            class="form-select">

                            <option value="">
                                Select Mother Tongue
                            </option>

                            @foreach($motherTongues as $motherTongue)

                            <option
                                value="{{ $motherTongue->mother_tongue }}"
                                @selected(
                                old('mother_tongue')==$motherTongue->mother_tongue
                                )>

                                {{ $motherTongue->mother_tongue }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Gotra --}}

                    <div class="col-md-4">

                        <label
                            for="gotra"
                            class="form-label">

                            Gotra

                        </label>


                        <input
                            type="text"
                            name="gotra"
                            id="gotra"
                            class="form-control"
                            value="{{ old('gotra') }}">

                    </div>


                    {{-- Manglik --}}

                    <div class="col-md-4">

                        <label
                            for="manglik"
                            class="form-label">

                            Manglik

                        </label>


                        <select
                            name="manglik"
                            id="manglik"
                            class="form-select">

                            <option value="">
                                Select
                            </option>

                            <option
                                value="Yes"
                                @selected(old('manglik')=='Yes' )>

                                Yes

                            </option>

                            <option
                                value="No"
                                @selected(old('manglik')=='No' )>

                                No

                            </option>

                        </select>

                    </div>


                    {{-- Marital Status --}}

                    <div class="col-md-4">

                        <label
                            for="marital_status"
                            class="form-label">

                            Marital Status

                        </label>


                        <select
                            name="marital_status"
                            id="marital_status"
                            class="form-select">

                            <option value="">
                                Select Marital Status
                            </option>

                            @foreach($maritalStatuses as $status)

                            <option
                                value="{{ $status->marital_status }}"
                                @selected(
                                old('marital_status')==$status->marital_status
                                )>

                                {{ $status->marital_status }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Children --}}

                    <div class="col-md-4">

                        <label
                            for="no_of_child"
                            class="form-label">

                            Number of Children

                        </label>


                        <input
                            type="number"
                            name="no_of_child"
                            id="no_of_child"
                            class="form-control"
                            min="0"
                            value="{{ old('no_of_child', 0) }}">

                    </div>


                    {{-- Birth Place --}}

                    <div class="col-md-4">

                        <label
                            for="birth_place"
                            class="form-label">

                            Birth Place

                        </label>


                        <input
                            type="text"
                            name="birth_place"
                            id="birth_place"
                            class="form-control"
                            value="{{ old('birth_place') }}">

                    </div>


                    {{-- Horoscope Needed --}}

                    <div class="col-md-4">

                        <label
                            for="horoscope_needed"
                            class="form-label">

                            Horoscope Needed

                        </label>


                        <select
                            name="horoscope_needed"
                            id="horoscope_needed"
                            class="form-select">

                            <option
                                value="0"
                                @selected(
                                old('horoscope_needed', '0' )=='0'
                                )>

                                No

                            </option>

                            <option
                                value="1"
                                @selected(
                                old('horoscope_needed')=='1'
                                )>

                                Yes

                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </div>



        {{-- =====================================================
            EDUCATION & CAREER
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">
                    <i class="bi bi-mortarboard me-2"></i>
                    Education & Career
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    {{-- Education --}}

                    <div class="col-md-4">

                        <label
                            for="education"
                            class="form-label">

                            Education

                        </label>


                        <select
                            name="education"
                            id="education"
                            class="form-select">

                            <option value="">
                                Select Education
                            </option>

                            @foreach($educations as $education)

                            <option
                                value="{{ $education->education }}"
                                @selected(
                                old('education')==$education->education
                                )>

                                {{ $education->education }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Other Qualifications --}}

                    <div class="col-md-4">

                        <label
                            for="any_other_qualifications"
                            class="form-label">

                            Other Qualifications

                        </label>


                        <input
                            type="text"
                            name="any_other_qualifications"
                            id="any_other_qualifications"
                            class="form-control"
                            value="{{ old('any_other_qualifications') }}">

                    </div>


                    {{-- Employed In --}}

                    <div class="col-md-4">

                        <label
                            for="employed_in"
                            class="form-label">

                            Employed In

                        </label>


                        <select
                            name="employed_in"
                            id="employed_in"
                            class="form-select">

                            <option value="">
                                Select
                            </option>

                            @foreach([
                            'Private',
                            'Government',
                            'Business',
                            'Self Employed',
                            'Not Working'
                            ] as $value)

                            <option
                                value="{{ $value }}"
                                @selected(
                                old('employed_in')==$value
                                )>

                                {{ $value }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Occupation --}}

                    <div class="col-md-4">

                        <label
                            for="occupation"
                            class="form-label">

                            Occupation

                        </label>


                        <select
                            name="occupation"
                            id="occupation"
                            class="form-select">

                            <option value="">
                                Select Occupation
                            </option>

                            @foreach($occupations as $occupation)

                            <option
                                value="{{ $occupation->occupation }}"
                                @selected(
                                old('occupation')==$occupation->occupation
                                )>

                                {{ $occupation->occupation }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Designation --}}

                    <div class="col-md-4">

                        <label
                            for="designation"
                            class="form-label">

                            Designation

                        </label>


                        <input
                            type="text"
                            name="designation"
                            id="designation"
                            class="form-control"
                            value="{{ old('designation') }}">

                    </div>


                    {{-- Organization --}}

                    <div class="col-md-4">

                        <label
                            for="organization_name"
                            class="form-label">

                            Organization Name

                        </label>


                        <input
                            type="text"
                            name="organization_name"
                            id="organization_name"
                            class="form-control"
                            value="{{ old('organization_name') }}">

                    </div>


                    {{-- Job Location --}}

                    <div class="col-md-4">

                        <label
                            for="job_location"
                            class="form-label">

                            Job Location

                        </label>


                        <input
                            type="text"
                            name="job_location"
                            id="job_location"
                            class="form-control"
                            value="{{ old('job_location') }}">

                    </div>


                    {{-- Annual Income --}}

                    <div class="col-md-4">

                        <label
                            for="annual_income"
                            class="form-label">

                            Annual Income

                        </label>


                        <select
                            name="annual_income"
                            id="annual_income"
                            class="form-select">

                            <option value="">
                                Select Annual Income
                            </option>

                            @foreach($annualIncomes as $income)

                            <option
                                value="{{ $income->annual_income }}"
                                @selected(
                                old('annual_income')==$income->annual_income
                                )>

                                {{ $income->annual_income }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- About Education --}}

                    <div class="col-md-6">

                        <label
                            for="about_my_education"
                            class="form-label">

                            About My Education

                        </label>


                        <textarea
                            name="about_my_education"
                            id="about_my_education"
                            class="form-control"
                            rows="3">{{ old('about_my_education') }}</textarea>

                    </div>


                    {{-- About Career --}}

                    <div class="col-md-6">

                        <label
                            for="about_my_career"
                            class="form-label">

                            About My Career

                        </label>


                        <textarea
                            name="about_my_career"
                            id="about_my_career"
                            class="form-control"
                            rows="3">{{ old('about_my_career') }}</textarea>

                    </div>

                </div>

            </div>

        </div>



        {{-- =====================================================
            MEMBER LOCATION
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">
                    <i class="bi bi-geo-alt me-2"></i>
                    Current Location
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    {{-- Country --}}

                    <div class="col-md-4">

                        <label
                            for="country_living_in"
                            class="form-label">

                            Country

                        </label>


                        <select
                            name="country_living_in"
                            id="country_living_in"
                            class="form-select">

                            <option value="">
                                Select Country
                            </option>

                            @foreach($countries as $country)

                            <option
                                value="{{ $country->name }}"
                                data-id="{{ $country->id }}"
                                @selected(
                                old('country_living_in')==$country->name
                                )>

                                {{ $country->name }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- State --}}

                    <div class="col-md-4">

                        <label
                            for="state_living_in"
                            class="form-label">

                            State

                        </label>


                        <select
                            name="state_living_in"
                            id="state_living_in"
                            class="form-select">

                            <option value="">
                                Select State
                            </option>

                        </select>

                    </div>


                    {{-- City --}}

                    <div class="col-md-4">

                        <label
                            for="city_living_in"
                            class="form-label">

                            City

                        </label>


                        <select
                            name="city_living_in"
                            id="city_living_in"
                            class="form-select">

                            <option value="">
                                Select City
                            </option>

                        </select>

                    </div>


                    {{-- Address --}}

                    <div class="col-md-8">

                        <label
                            for="address_living_in"
                            class="form-label">

                            Address

                        </label>


                        <textarea
                            name="address_living_in"
                            id="address_living_in"
                            class="form-control"
                            rows="3">{{ old('address_living_in') }}</textarea>

                    </div>


                    {{-- Native Place --}}

                    <div class="col-md-4">

                        <label
                            for="native_place"
                            class="form-label">

                            Native Place

                        </label>


                        <input
                            type="text"
                            name="native_place"
                            id="native_place"
                            class="form-control"
                            value="{{ old('native_place') }}">

                    </div>

                </div>

            </div>

        </div>



        {{-- =====================================================
            FAMILY INFORMATION
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>
                    Family Information
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    {{-- Family Type --}}

                    <div class="col-md-4">

                        <label
                            for="family_type"
                            class="form-label">

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
                                @selected(old('family_type')=='Joint' )>

                                Joint

                            </option>

                            <option
                                value="Nuclear"
                                @selected(old('family_type')=='Nuclear' )>

                                Nuclear

                            </option>

                        </select>

                    </div>


                    {{-- Family Status --}}

                    <div class="col-md-4">

                        <label
                            for="family_status"
                            class="form-label">

                            Family Status

                        </label>


                        <select
                            name="family_status"
                            id="family_status"
                            class="form-select">

                            <option value="">
                                Select Family Status
                            </option>

                            @foreach($familyStatuses as $status)

                            <option
                                value="{{ $status->family_status }}"
                                @selected(
                                old('family_status')==$status->family_status
                                )>

                                {{ $status->family_status }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Family Income --}}

                    <div class="col-md-4">

                        <label
                            for="family_income"
                            class="form-label">

                            Family Income

                        </label>


                        <input
                            type="text"
                            name="family_income"
                            id="family_income"
                            class="form-control"
                            value="{{ old('family_income') }}">

                    </div>


                    {{-- Father Name --}}

                    <div class="col-md-6">

                        <label
                            for="father_name"
                            class="form-label">

                            Father's Name

                        </label>


                        <input
                            type="text"
                            name="father_name"
                            id="father_name"
                            class="form-control"
                            value="{{ old('father_name') }}">

                    </div>


                    {{-- Father Occupation --}}

                    <div class="col-md-6">

                        <label
                            for="father_occupation"
                            class="form-label">

                            Father's Occupation

                        </label>


                        <input
                            type="text"
                            name="father_occupation"
                            id="father_occupation"
                            class="form-control"
                            value="{{ old('father_occupation') }}">

                    </div>


                    {{-- Mother Name --}}

                    <div class="col-md-6">

                        <label
                            for="mother_name"
                            class="form-label">

                            Mother's Name

                        </label>


                        <input
                            type="text"
                            name="mother_name"
                            id="mother_name"
                            class="form-control"
                            value="{{ old('mother_name') }}">

                    </div>


                    {{-- Mother Occupation --}}

                    <div class="col-md-6">

                        <label
                            for="mother_occupation"
                            class="form-label">

                            Mother's Occupation

                        </label>


                        <input
                            type="text"
                            name="mother_occupation"
                            id="mother_occupation"
                            class="form-control"
                            value="{{ old('mother_occupation') }}">

                    </div>


                    {{-- Brothers --}}

                    <div class="col-md-3">

                        <label
                            for="no_of_brothers"
                            class="form-label">

                            No. of Brothers

                        </label>


                        <input
                            type="number"
                            name="no_of_brothers"
                            id="no_of_brothers"
                            class="form-control"
                            min="0"
                            value="{{ old('no_of_brothers', 0) }}">

                    </div>


                    {{-- Sisters --}}

                    <div class="col-md-3">

                        <label
                            for="no_of_sisters"
                            class="form-label">

                            No. of Sisters

                        </label>


                        <input
                            type="number"
                            name="no_of_sisters"
                            id="no_of_sisters"
                            class="form-control"
                            min="0"
                            value="{{ old('no_of_sisters', 0) }}">

                    </div>


                    {{-- Married Brothers --}}

                    <div class="col-md-3">

                        <label
                            for="married_brothers"
                            class="form-label">

                            Married Brothers

                        </label>


                        <input
                            type="number"
                            name="married_brothers"
                            id="married_brothers"
                            class="form-control"
                            min="0"
                            value="{{ old('married_brothers', 0) }}">

                    </div>


                    {{-- Married Sisters --}}

                    <div class="col-md-3">

                        <label
                            for="married_sisters"
                            class="form-label">

                            Married Sisters

                        </label>


                        <input
                            type="number"
                            name="married_sisters"
                            id="married_sisters"
                            class="form-control"
                            min="0"
                            value="{{ old('married_sisters', 0) }}">

                    </div>


                    {{-- About Family --}}

                    <div class="col-md-12">

                        <label
                            for="about_family"
                            class="form-label">

                            About Family

                        </label>


                        <textarea
                            name="about_family"
                            id="about_family"
                            class="form-control"
                            rows="4">{{ old('about_family') }}</textarea>

                    </div>

                </div>

            </div>

        </div>



        {{-- =====================================================
            LIFESTYLE
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">
                    <i class="bi bi-heart me-2"></i>
                    Lifestyle & About Me
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    {{-- Diet --}}

                    <div class="col-md-4">

                        <label
                            for="diet"
                            class="form-label">

                            Diet

                        </label>


                        <select
                            name="diet"
                            id="diet"
                            class="form-select">

                            <option value="">
                                Select
                            </option>

                            @foreach([
                            'Vegetarian',
                            'Non-Vegetarian',
                            'Eggetarian',
                            'Vegan'
                            ] as $value)

                            <option
                                value="{{ $value }}"
                                @selected(old('diet')==$value)>

                                {{ $value }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Drinking --}}

                    <div class="col-md-4">

                        <label
                            for="is_drinking"
                            class="form-label">

                            Drinking

                        </label>


                        <select
                            name="is_drinking"
                            id="is_drinking"
                            class="form-select">

                            <option value="">
                                Select
                            </option>

                            @foreach([
                            'No',
                            'Occasionally',
                            'Yes'
                            ] as $value)

                            <option
                                value="{{ $value }}"
                                @selected(
                                old('is_drinking')==$value
                                )>

                                {{ $value }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Smoking --}}

                    <div class="col-md-4">

                        <label
                            for="is_smoking"
                            class="form-label">

                            Smoking

                        </label>


                        <select
                            name="is_smoking"
                            id="is_smoking"
                            class="form-select">

                            <option value="">
                                Select
                            </option>

                            @foreach([
                            'No',
                            'Occasionally',
                            'Yes'
                            ] as $value)

                            <option
                                value="{{ $value }}"
                                @selected(
                                old('is_smoking')==$value
                                )>

                                {{ $value }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Disability --}}

                    <div class="col-md-4">

                        <label
                            for="any_disability"
                            class="form-label">

                            Any Disability

                        </label>


                        <select
                            name="any_disability"
                            id="any_disability"
                            class="form-select">

                            <option value="">
                                Select
                            </option>

                            <option
                                value="No"
                                @selected(
                                old('any_disability')=='No'
                                )>

                                No

                            </option>

                            <option
                                value="Yes"
                                @selected(
                                old('any_disability')=='Yes'
                                )>

                                Yes

                            </option>

                        </select>

                    </div>


                    {{-- About Me --}}

                    <div class="col-md-8">

                        <label
                            for="about_me"
                            class="form-label">

                            About Me

                        </label>


                        <textarea
                            name="about_me"
                            id="about_me"
                            class="form-control"
                            rows="4">{{ old('about_me') }}</textarea>

                    </div>

                </div>

            </div>

        </div>



        {{-- =====================================================
            PARTNER PREFERENCES
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">
                    <i class="bi bi-person-heart me-2"></i>
                    Partner Preferences
                </h5>

            </div>


            <div class="card-body">


                {{-- =================================================
                    BASIC PREFERENCES
                ================================================== --}}

                <h6 class="fw-semibold mb-3">
                    Basic Preferences
                </h6>


                <div class="row g-3">


                    {{-- Looking For --}}

                    <div class="col-md-4">

                        <label
                            for="looking_for"
                            class="form-label">

                            Looking For

                        </label>


                        <input
                            type="text"
                            name="looking_for"
                            id="looking_for"
                            class="form-control"
                            value="{{ old('looking_for') }}">

                    </div>


                    {{-- Age From --}}

                    <div class="col-md-4">

                        <label
                            for="partner_age_from"
                            class="form-label">

                            Partner Age From

                        </label>


                        <input
                            type="number"
                            name="partner_age_from"
                            id="partner_age_from"
                            class="form-control"
                            min="18"
                            value="{{ old('partner_age_from') }}">

                    </div>


                    {{-- Age To --}}

                    <div class="col-md-4">

                        <label
                            for="partner_age_to"
                            class="form-label">

                            Partner Age To

                        </label>


                        <input
                            type="number"
                            name="partner_age_to"
                            id="partner_age_to"
                            class="form-control"
                            min="18"
                            value="{{ old('partner_age_to') }}">

                    </div>

                </div>


                <hr class="my-4">


                {{-- =================================================
                    PARTNER RELIGION
                ================================================== --}}

                <h6 class="fw-semibold mb-3">
                    Religion & Background
                </h6>


                <div class="row g-3">

                    {{-- Partner Religion --}}

                    <div class="col-md-4">

                        <label
                            for="partner_religion"
                            class="form-label">

                            Partner Religion

                        </label>


                        <select
                            name="partner_religion"
                            id="partner_religion"
                            class="form-select">

                            <option value="">
                                Select Religion
                            </option>

                            @foreach($religions as $religion)

                            <option
                                value="{{ $religion->religion }}"
                                @selected(
                                old('partner_religion')==$religion->religion
                                )>

                                {{ $religion->religion }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Partner Cast --}}

                    <div class="col-md-4">

                        <label
                            for="partner_cast"
                            class="form-label">

                            Partner Cast

                        </label>


                        <select
                            name="partner_cast"
                            id="partner_cast"
                            class="form-select">

                            <option value="">
                                Select Cast
                            </option>

                            @foreach($casts as $cast)

                            <option
                                value="{{ $cast->cast }}"
                                @selected(
                                old('partner_cast')==$cast->cast
                                )>

                                {{ $cast->cast }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Partner Mother Tongue --}}

                    <div class="col-md-4">

                        <label
                            for="partner_mothertongue"
                            class="form-label">

                            Partner Mother Tongue

                        </label>


                        <select
                            name="partner_mothertongue"
                            id="partner_mothertongue"
                            class="form-select">

                            <option value="">
                                Select Mother Tongue
                            </option>

                            @foreach($motherTongues as $motherTongue)

                            <option
                                value="{{ $motherTongue->mother_tongue }}"
                                @selected(
                                old('partner_mothertongue')==$motherTongue->mother_tongue
                                )>

                                {{ $motherTongue->mother_tongue }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Partner Manglik --}}

                    <div class="col-md-4">

                        <label
                            for="is_partner_manglik"
                            class="form-label">

                            Partner Manglik

                        </label>


                        <select
                            name="is_partner_manglik"
                            id="is_partner_manglik"
                            class="form-select">

                            <option value="">
                                Select
                            </option>

                            <option
                                value="Yes"
                                @selected(
                                old('is_partner_manglik')=='Yes'
                                )>

                                Yes

                            </option>

                            <option
                                value="No"
                                @selected(
                                old('is_partner_manglik')=='No'
                                )>

                                No

                            </option>

                        </select>

                    </div>

                </div>


                <hr class="my-4">


                {{-- =================================================
                    PARTNER HEIGHT / EDUCATION / CAREER
                ================================================== --}}

                <h6 class="fw-semibold mb-3">
                    Education & Career
                </h6>


                <div class="row g-3">


                    {{-- Height From --}}

                    <div class="col-md-4">

                        <label
                            for="partner_height_from"
                            class="form-label">

                            Partner Height From

                        </label>


                        <select
                            name="partner_height_from"
                            id="partner_height_from"
                            class="form-select">

                            <option value="">
                                Select Height
                            </option>

                            @foreach($heights as $height)

                            @php
                            $heightValue =
                            $height->height_value
                            ?? $height->height;
                            @endphp

                            <option
                                value="{{ $heightValue }}"
                                @selected(
                                old('partner_height_from')==$heightValue
                                )>

                                {{ $height->height }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Height To --}}

                    <div class="col-md-4">

                        <label
                            for="partner_height_to"
                            class="form-label">

                            Partner Height To

                        </label>


                        <select
                            name="partner_height_to"
                            id="partner_height_to"
                            class="form-select">

                            <option value="">
                                Select Height
                            </option>

                            @foreach($heights as $height)

                            @php
                            $heightValue =
                            $height->height_value
                            ?? $height->height;
                            @endphp

                            <option
                                value="{{ $heightValue }}"
                                @selected(
                                old('partner_height_to')==$heightValue
                                )>

                                {{ $height->height }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Education --}}

                    <div class="col-md-4">

                        <label
                            for="partner_education"
                            class="form-label">

                            Partner Education

                        </label>


                        <select
                            name="partner_education"
                            id="partner_education"
                            class="form-select">

                            <option value="">
                                Select Education
                            </option>

                            @foreach($educations as $education)

                            <option
                                value="{{ $education->education }}"
                                @selected(
                                old('partner_education')==$education->education
                                )>

                                {{ $education->education }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Occupation --}}

                    <div class="col-md-4">

                        <label
                            for="partner_occupation"
                            class="form-label">

                            Partner Occupation

                        </label>


                        <select
                            name="partner_occupation"
                            id="partner_occupation"
                            class="form-select">

                            <option value="">
                                Select Occupation
                            </option>

                            @foreach($occupations as $occupation)

                            <option
                                value="{{ $occupation->occupation }}"
                                @selected(
                                old('partner_occupation')==$occupation->occupation
                                )>

                                {{ $occupation->occupation }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Income From --}}

                    <div class="col-md-4">

                        <label
                            for="partner_annual_income_from"
                            class="form-label">

                            Partner Annual Income From

                        </label>


                        <select
                            name="partner_annual_income_from"
                            id="partner_annual_income_from"
                            class="form-select">

                            <option value="">
                                Select Income
                            </option>

                            @foreach($annualIncomes as $income)

                            <option
                                value="{{ $income->annual_income }}"
                                @selected(
                                old('partner_annual_income_from')==$income->annual_income
                                )>

                                {{ $income->annual_income }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Income To --}}

                    <div class="col-md-4">

                        <label
                            for="partner_annual_income_to"
                            class="form-label">

                            Partner Annual Income To

                        </label>


                        <select
                            name="partner_annual_income_to"
                            id="partner_annual_income_to"
                            class="form-select">

                            <option value="">
                                Select Income
                            </option>

                            @foreach($annualIncomes as $income)

                            <option
                                value="{{ $income->annual_income }}"
                                @selected(
                                old('partner_annual_income_to')==$income->annual_income
                                )>

                                {{ $income->annual_income }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                </div>


                <hr class="my-4">


                {{-- =================================================
                    PARTNER LOCATION
                ================================================== --}}

                <h6 class="fw-semibold mb-3">
                    Partner Location
                </h6>


                <div class="row g-3">

                    {{-- Partner Country --}}

                    <div class="col-md-4">

                        <label
                            for="partner_country"
                            class="form-label">

                            Partner Country

                        </label>


                        <select
                            name="partner_country"
                            id="partner_country"
                            class="form-select">

                            <option value="">
                                Select Country
                            </option>

                            @foreach($countries as $country)

                            <option
                                value="{{ $country->name }}"
                                data-id="{{ $country->id }}"
                                @selected(
                                old('partner_country')==$country->name
                                )>

                                {{ $country->name }}

                            </option>

                            @endforeach

                        </select>

                    </div>
                    {{-- Partner State --}}

                    <div class="col-md-6">

                        <label
                            for="partner_state"
                            class="form-label">

                            Partner State

                        </label>


                        <select
                            name="partner_state"
                            id="partner_state"
                            class="form-select">

                            <option value="">
                                Select State
                            </option>

                        </select>

                    </div>


                    {{-- Partner City --}}

                    <div class="col-md-6">

                        <label
                            for="partner_city"
                            class="form-label">

                            Partner City

                        </label>


                        <select
                            name="partner_city"
                            id="partner_city"
                            class="form-select">

                            <option value="">
                                Select City
                            </option>

                        </select>

                    </div>

                </div>


                <hr class="my-4">


                {{-- =================================================
                    PARTNER LIFESTYLE
                ================================================== --}}

                <h6 class="fw-semibold mb-3">
                    Lifestyle
                </h6>


                <div class="row g-3">


                    {{-- Partner Diet --}}

                    <div class="col-md-4">

                        <label
                            for="partner_diet"
                            class="form-label">

                            Partner Diet

                        </label>


                        <select
                            name="partner_diet"
                            id="partner_diet"
                            class="form-select">

                            <option value="">
                                Select
                            </option>

                            @foreach([
                            'Vegetarian',
                            'Non-Vegetarian',
                            'Eggetarian',
                            'Vegan'
                            ] as $value)

                            <option
                                value="{{ $value }}"
                                @selected(
                                old('partner_diet')==$value
                                )>

                                {{ $value }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Partner Smoking --}}

                    <div class="col-md-4">

                        <label
                            for="is_partner_smoking"
                            class="form-label">

                            Partner Smoking

                        </label>


                        <select
                            name="is_partner_smoking"
                            id="is_partner_smoking"
                            class="form-select">

                            <option value="">
                                Select
                            </option>

                            @foreach([
                            'No',
                            'Occasionally',
                            'Yes'
                            ] as $value)

                            <option
                                value="{{ $value }}"
                                @selected(
                                old('is_partner_smoking')==$value
                                )>

                                {{ $value }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Partner Drinking --}}

                    <div class="col-md-4">

                        <label
                            for="is_partner_drinking"
                            class="form-label">

                            Partner Drinking

                        </label>


                        <select
                            name="is_partner_drinking"
                            id="is_partner_drinking"
                            class="form-select">

                            <option value="">
                                Select
                            </option>

                            @foreach([
                            'No',
                            'Occasionally',
                            'Yes'
                            ] as $value)

                            <option
                                value="{{ $value }}"
                                @selected(
                                old('is_partner_drinking')==$value
                                )>

                                {{ $value }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- About Partner --}}

                    <div class="col-md-12">

                        <label
                            for="about_my_partner"
                            class="form-label">

                            About My Partner

                        </label>


                        <textarea
                            name="about_my_partner"
                            id="about_my_partner"
                            class="form-control"
                            rows="4">{{ old('about_my_partner') }}</textarea>

                    </div>

                </div>

            </div>

        </div>



        {{-- =====================================================
            ACCOUNT & ADMIN SETTINGS
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Account & Profile Settings
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    {{-- Password --}}

                    <div class="col-md-4">

                        <label
                            for="password"
                            class="form-label">

                            Password

                            <span class="text-danger">*</span>

                        </label>


                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            minlength="8"
                            required>

                    </div>


                    {{-- Member Type --}}

                    <div class="col-md-4">

                        <label
                            for="member_type"
                            class="form-label">

                            Member Type

                        </label>


                        <input
                            type="text"
                            name="member_type"
                            id="member_type"
                            class="form-control"
                            value="{{ old('member_type', 'free') }}">

                    </div>


                    {{-- Active --}}

                    <div class="col-md-4">

                        <label
                            for="active"
                            class="form-label">

                            Account Status

                        </label>


                        <select
                            name="active"
                            id="active"
                            class="form-select">

                            <option
                                value="No"
                                @selected(
                                old('active', 'No' )=='No'
                                )>

                                Inactive

                            </option>

                            <option
                                value="Yes"
                                @selected(
                                old('active')=='Yes'
                                )>

                                Active

                            </option>

                        </select>

                    </div>


                    {{-- Trusted --}}

                    <div class="col-md-4">

                        <label
                            for="is_trusted"
                            class="form-label">

                            Trusted Profile

                        </label>


                        <select
                            name="is_trusted"
                            id="is_trusted"
                            class="form-select">

                            <option
                                value="No"
                                @selected(
                                old('is_trusted', 'No' )=='No'
                                )>

                                No

                            </option>

                            <option
                                value="Yes"
                                @selected(
                                old('is_trusted')=='Yes'
                                )>

                                Yes

                            </option>

                        </select>

                    </div>


                    {{-- Promoted --}}

                    <div class="col-md-4">

                        <label
                            for="promoted"
                            class="form-label">

                            Promoted

                        </label>


                        <select
                            name="promoted"
                            id="promoted"
                            class="form-select">

                            <option
                                value="No"
                                @selected(
                                old('promoted', 'No' )=='No'
                                )>

                                No

                            </option>

                            <option
                                value="Yes"
                                @selected(
                                old('promoted')=='Yes'
                                )>

                                Yes

                            </option>

                        </select>

                    </div>


                    {{-- Profile Visibility --}}

                    <div class="col-md-4">

                        <label
                            for="profile_hide"
                            class="form-label">

                            Profile Visibility

                        </label>


                        <select
                            name="profile_hide"
                            id="profile_hide"
                            class="form-select">

                            <option
                                value="No"
                                @selected(
                                old('profile_hide', 'No' )=='No'
                                )>

                                Visible

                            </option>

                            <option
                                value="Yes"
                                @selected(
                                old('profile_hide')=='Yes'
                                )>

                                Hidden

                            </option>

                        </select>

                    </div>


                    {{-- Relationship Manager --}}

                    <div class="col-md-6">

                        <label
                            for="relationship_manager"
                            class="form-label">

                            Relationship Manager

                        </label>


                        <input
                            type="text"
                            name="relationship_manager"
                            id="relationship_manager"
                            class="form-control"
                            value="{{ old('relationship_manager') }}">

                    </div>


                    {{-- Remarks --}}

                    <div class="col-md-6">

                        <label
                            for="remarks"
                            class="form-label">

                            Remarks

                        </label>


                        <textarea
                            name="remarks"
                            id="remarks"
                            class="form-control"
                            rows="3">{{ old('remarks') }}</textarea>

                    </div>

                </div>

            </div>

        </div>



        {{-- =====================================================
            SUBMIT
        ====================================================== --}}

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="{{ route('admin.members.index') }}"
                        class="btn btn-light">

                        Cancel

                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-person-plus me-1"></i>

                        Create Member

                    </button>

                </div>

            </div>

        </div>


    </form>

</div>

@endsection