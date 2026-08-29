@extends('admin.layout')

@section('content')

@push('styles')
<style>
    .member-profile-section .card-header {
        padding: 1.5rem 1.5rem 0;
        background: #fff;
        border: 0;
    }

    .member-profile-section .card-header h5 {
        display: flex;
        align-items: center;
        margin-bottom: 0;
        font-size: 1.1rem;
    }

    .member-profile-section .card-header h5>i {
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

    .member-profile-section .card-body {
        padding: 1.5rem;
    }
</style>
@endpush

@if(session('success'))

<div class="alert alert-success alert-dismissible fade show">

    {{ session('success') }}

    <button type="button"
        class="btn-close"
        data-bs-dismiss="alert">
    </button>

</div>

@endif

<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">

        <div>
            <h1 class="h3 mb-1">Member Profile</h1>

            <div class="text-muted">
                Profile ID:
                <strong>{{ $member->profile_id }}</strong>
            </div>
        </div>

        <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2">

            @php
            $backUrl = route('admin.members.index');

            if (!empty($returnUrl)) {
            $parsedUrl = parse_url($returnUrl);

            if (
            !isset($parsedUrl['host']) ||
            $parsedUrl['host'] === request()->getHost()
            ) {
            $backUrl = $returnUrl;
            }
            }
            @endphp

            <button
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#shareProfileModal">
                <i class="bi bi-share me-1"></i>
                Share Profile
            </button>

            <a
                href="{{ $backUrl }}"
                class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left me-1"></i>
                Back to Members

            </a>

        </div>

    </div>


    {{-- Profile Header Card --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body p-4">

            <div class="row align-items-center">

                {{-- Profile Photo --}}
                <div class="col-md-2 text-center mb-3 mb-md-0">

                    @if($member->photo_url)

                    <img
                        src="{{ $member->photo_url }}"
                        alt="{{ $member->full_name }}"
                        class="w-100 h-100 rounded object-fit-cover"
                        loading="lazy"
                        style="
                                width: 130px;
                                height: 130px;
                                object-fit: cover;
                            ">

                    @else

                    <div
                        class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto"
                        style="
                                width: 130px;
                                height: 130px;
                            ">
                        <span class="text-muted">
                            No Photo
                        </span>
                    </div>

                    @endif

                </div>


                {{-- Basic Information --}}
                <div class="col-md-6">

                    <h2 class="h4 mb-2">
                        {{ $member->full_name }}
                    </h2>

                    <div class="mb-2">

                        <span class="badge bg-light text-dark border me-1">
                            {{ $member->profile_id }}
                        </span>

                        @if(strtolower(trim($member->active ?? '')) === 'yes')

                        <span class="badge bg-success">
                            Active
                        </span>

                        @else

                        <span class="badge bg-secondary">
                            Inactive
                        </span>

                        @endif

                    </div>


                    <div class="text-muted">

                        @if(!empty($member->gender))
                        <span class="me-3">
                            {{ $member->gender }}
                        </span>
                        @endif

                        @if(!empty($member->birth_date_time))
                        <span class="me-3">
                            {{ $member->birth_date_time }}
                        </span>
                        @endif

                    </div>


                    @if(!empty($member->mobile_number))

                    <div class="mt-2">

                        <strong>Mobile:</strong>
                        {{ $member->mobile_number }}

                    </div>

                    @endif


                    @if(!empty($member->email))

                    <div>

                        <strong>Email:</strong>
                        {{ $member->email }}

                    </div>

                    @endif

                </div>


                {{-- Profile Completion --}}
                <div class="col-md-4 mt-4 mt-md-0">

                    <div class="d-flex justify-content-between mb-2">

                        <span class="fw-semibold">
                            Profile Completion
                        </span>

                        <span class="fw-bold">
                            {{ $profileCompletion }}%
                        </span>

                    </div>


                    <div
                        class="progress"
                        style="height: 10px;">

                        <div
                            class="progress-bar"
                            role="progressbar"
                            style="width: {{ $profileCompletion }}%;"
                            aria-valuenow="{{ $profileCompletion }}"
                            aria-valuemin="0"
                            aria-valuemax="100"></div>

                    </div>


                    <div class="small text-muted mt-2">

                        {{ $completedFields }}
                        of
                        {{ $totalFields }}
                        profile fields completed

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
    Photos
========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0">
                <i class="bi bi-images me-2"></i>
                Photos
            </h5>
        </div>

        <div class="card-body">

            {{-- Main Profile Photo --}}
            <div class="mb-4">

                <h6 class="text-muted mb-3">
                    Profile Photo
                </h6>

                @if(!empty($member->photo))

                <div class="position-relative d-inline-block">

                    <img
                        src="{{ $member->photo_url }}"
                        alt="{{ $member->full_name }}"
                        class="rounded-3 shadow-sm"
                        style="width:180px;height:180px;object-fit:cover;"
                        loading="lazy"
                        decoding="async">

                </div>

                @else

                <div
                    class="d-flex align-items-center justify-content-center bg-light rounded-3"
                    style="width:180px;height:180px;">

                    <div class="text-center text-muted">
                        <i class="bi bi-person fs-1"></i>
                        <div>No profile photo</div>
                    </div>

                </div>

                @endif

            </div>

            {{-- =========================================================
    PHOTO MANAGEMENT
========================================================= --}}

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-0 pt-4 px-4">

                    <div class="d-flex align-items-center">

                        <div
                            class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3"
                            style="width:42px;height:42px;">

                            <i class="bi bi-camera fs-5"></i>

                        </div>

                        <div>

                            <h5 class="mb-1">
                                Photo Management
                            </h5>

                            <small class="text-muted">
                                Manage the member's profile and gallery photos.
                            </small>

                        </div>

                    </div>

                </div>


                <div class="card-body px-4 pb-4">

                    {{-- =====================================================
            SUCCESS MESSAGE
        ====================================================== --}}

                    @if(session('success'))

                    <div class="alert alert-success alert-dismissible fade show">

                        <i class="bi bi-check-circle me-2"></i>

                        {{ session('success') }}

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                        </button>

                    </div>

                    @endif


                    {{-- =====================================================
            ERROR MESSAGE
        ====================================================== --}}

                    @if($errors->has('photo'))

                    <div class="alert alert-danger">

                        <i class="bi bi-exclamation-triangle me-2"></i>

                        {{ $errors->first('photo') }}

                    </div>

                    @endif


                    <div class="row g-4 align-items-start">

                        {{-- =================================================
                CURRENT PROFILE PHOTO
            ================================================== --}}

                        <div class="col-md-4">

                            <div class="text-center">

                                <h6 class="text-muted mb-3">
                                    Current Profile Photo
                                </h6>


                                @if(!empty($member->photo))

                                <div
                                    class="mx-auto mb-3"
                                    style="width:180px;height:180px;">

                                    <img
                                        src="{{ $member->photo_url }}"
                                        alt="{{ $member->full_name }}"
                                        class="w-100 h-100 rounded object-fit-cover"
                                        loading="lazy">

                                </div>

                                @else

                                <div
                                    class="mx-auto mb-3 rounded bg-light d-flex align-items-center justify-content-center"
                                    style="width:180px;height:180px;">

                                    <i class="bi bi-person fs-1 text-muted"></i>

                                </div>

                                @endif


                                <div class="small text-muted">

                                    Profile ID:
                                    <strong>
                                        {{ $member->profile_id }}
                                    </strong>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                UPLOAD PHOTO
            ================================================== --}}

                        <div class="col-md-8">

                            <div class="border rounded p-4">

                                <h6 class="mb-3">
                                    <i class="bi bi-cloud-arrow-up me-2"></i>
                                    Upload Gallery Photo
                                </h6>


                                <form
                                    action="{{ route('admin.members.photos.store', [
                            'memberId' => $member->id
                        ]) }}"
                                    method="POST"
                                    enctype="multipart/form-data">

                                    @csrf


                                    <div class="mb-3">

                                        <label
                                            for="member_photo"
                                            class="form-label">

                                            Select Photo

                                        </label>


                                        <input
                                            type="file"
                                            class="form-control"
                                            id="member_photo"
                                            name="photo"
                                            accept=".jpg,.jpeg,.png,.webp"
                                            required>


                                        <div class="form-text">

                                            JPG, JPEG, PNG or WebP.
                                            Maximum size: 10 MB.

                                        </div>

                                    </div>


                                    {{-- Preview --}}

                                    <div
                                        id="photoPreviewContainer"
                                        class="mb-3 d-none">

                                        <label class="form-label">
                                            Preview
                                        </label>

                                        <div>

                                            <img
                                                id="photoPreview"
                                                src=""
                                                alt="Photo preview"
                                                style="
                                        width:180px;
                                        height:180px;
                                        object-fit:cover;
                                        border-radius:8px;
                                    ">

                                        </div>

                                    </div>


                                    <button
                                        type="submit"
                                        class="btn btn-primary">

                                        <i class="bi bi-upload me-1"></i>

                                        Upload Photo

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- Gallery --}}
            <div>

                <h6 class="text-muted mb-3">
                    Gallery
                </h6>

                @if($galleryPhotos->count())

                <div class="row g-3">

                    @foreach($galleryPhotos as $photo)

                    <div class="col-6 col-md-4 col-lg-3">

                        <div class="card border-0 shadow-sm h-100">

                            <div class="position-relative">

                                <img
                                    src="{{ $photo->photo_url }}"
                                    alt="Member photo"
                                    class="card-img-top"
                                    style="height:180px;object-fit:cover;"
                                    loading="lazy"
                                    decoding="async">

                            </div>

                            <div class="card-body p-2">

                                <div class="d-flex justify-content-between align-items-center">

                                    <small class="text-muted">
                                        Photo #{{ $photo->id }}
                                    </small>

                                    @if($photo->photo_approved === 'Yes')

                                    <span class="badge bg-success">
                                        Approved
                                    </span>

                                    @else

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                    @endif

                                </div>

                                <div class="mt-2">

                                    @if($photo->photo_privacy == 1)

                                    <small class="text-muted">
                                        <i class="bi bi-globe me-1"></i>
                                        Public
                                    </small>

                                    @else

                                    <small class="text-muted">
                                        <i class="bi bi-lock me-1"></i>
                                        Private
                                    </small>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>

                @else

                <div class="text-center py-4 text-muted">

                    <i class="bi bi-images fs-1"></i>

                    <p class="mt-2 mb-0">
                        No gallery photos found.
                    </p>

                </div>

                @endif

            </div>

        </div>

    </div>


    {{-- Basic Information --}}
    <div class="card border-0 shadow-sm mb-4 member-profile-section">

        <div class="card-header">

            <h5 class="mb-0">
                <i class="bi bi-person me-2"></i>
                Basic Information
            </h5>

        </div>


        <div class="card-body">

            <div class="row">

                <div class="col-md-4 mb-3">

                    <small class="text-muted d-block">
                        Full Name
                    </small>

                    <strong>
                        {{ $member->full_name ?: '-' }}
                    </strong>

                </div>


                <div class="col-md-4 mb-3">

                    <small class="text-muted d-block">
                        Gender
                    </small>

                    <strong>
                        {{ $member->gender ?: '-' }}
                    </strong>

                </div>


                <div class="col-md-4 mb-3">

                    <small class="text-muted d-block">
                        Date of Birth
                    </small>

                    <strong>
                        {{ $member->birth_date_time ?: '-' }}
                    </strong>

                </div>


                <div class="col-md-4 mb-3">

                    <small class="text-muted d-block">
                        Height
                    </small>

                    <strong>
                        {{ $member->height ?: '-' }}
                    </strong>

                </div>


                <div class="col-md-4 mb-3">

                    <small class="text-muted d-block">
                        Blood Group
                    </small>

                    <strong>
                        {{ $member->blood_group ?: '-' }}
                    </strong>

                </div>


                <div class="col-md-4 mb-3">

                    <small class="text-muted d-block">
                        Marital Status
                    </small>

                    <strong>
                        {{ $member->marital_status ?: '-' }}
                    </strong>

                </div>


                <div class="col-md-4 mb-3">

                    <small class="text-muted d-block">
                        Religion
                    </small>

                    <strong>
                        {{ $member->religion ?: '-' }}
                    </strong>

                </div>


                <div class="col-md-4 mb-3">

                    <small class="text-muted d-block">
                        Mother Tongue
                    </small>

                    <strong>
                        {{ $member->mother_tongue ?: '-' }}
                    </strong>

                </div>


                <div class="col-md-4 mb-3">

                    <small class="text-muted d-block">
                        Caste
                    </small>

                    <strong>
                        {{ $member->cast ?: '-' }}
                    </strong>

                </div>

            </div>

        </div>

    </div>

    {{-- About Me --}}
    <div class="card border-0 shadow-sm mb-4 member-profile-section">

        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-chat-heart me-2"></i>
                About Me
            </h5>
        </div>

        <div class="card-body">

            @if(!empty($member->about_me))

            <p class="mb-0">
                {{ $member->about_me }}
            </p>

            @else

            <p class="text-muted mb-0">
                No information provided.
            </p>

            @endif

        </div>

    </div>


    {{-- Education & Career --}}
    <div class="card border-0 shadow-sm mb-4 member-profile-section">

        <div class="card-header">

            <h5 class="mb-0">
                <i class="bi bi-mortarboard me-2"></i>
                Education & Career
            </h5>

        </div>

        <div class="card-body">

            <div class="row">

                {{-- Education --}}
                <div class="col-md-6 mb-4">

                    <h6 class="fw-semibold mb-3">
                        Education
                    </h6>

                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Education
                        </small>

                        <strong>
                            {{ $member->education ?: '-' }}
                        </strong>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Other Qualifications
                        </small>

                        <strong>
                            {{ $member->any_other_qualifications ?: '-' }}
                        </strong>

                    </div>


                    <div>

                        <small class="text-muted d-block">
                            About Education
                        </small>

                        <div>
                            {{ $member->about_my_education ?: '-' }}
                        </div>

                    </div>

                </div>


                {{-- Career --}}
                <div class="col-md-6 mb-4">

                    <h6 class="fw-semibold mb-3">
                        Career
                    </h6>

                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Employed In
                        </small>

                        <strong>
                            {{ $member->employed_in ?: '-' }}
                        </strong>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Occupation
                        </small>

                        <strong>
                            {{ $member->occupation ?: '-' }}
                        </strong>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Designation
                        </small>

                        <strong>
                            {{ $member->designation ?: '-' }}
                        </strong>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Organization
                        </small>

                        <strong>
                            {{ $member->organization_name ?: '-' }}
                        </strong>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Job Location
                        </small>

                        <strong>
                            {{ $member->job_location ?: '-' }}
                        </strong>

                    </div>


                    <div>

                        <small class="text-muted d-block">
                            Annual Income
                        </small>

                        <strong>
                            {{ $member->annual_income ?: '-' }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Location --}}
    <div class="card border-0 shadow-sm mb-4 member-profile-section">

        <div class="card-header">

            <h5 class="mb-0">
                <i class="bi bi-geo-alt me-2"></i>
                Location
            </h5>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4 mb-3">

                    <small class="text-muted d-block">
                        Country
                    </small>

                    <strong>
                        {{ $member->country_living_in ?: '-' }}
                    </strong>

                </div>


                <div class="col-md-4 mb-3">

                    <small class="text-muted d-block">
                        State
                    </small>

                    <strong>
                        {{ $member->state_living_in ?: '-' }}
                    </strong>

                </div>


                <div class="col-md-4 mb-3">

                    <small class="text-muted d-block">
                        City
                    </small>

                    <strong>
                        {{ $member->city_living_in ?: '-' }}
                    </strong>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted d-block">
                        Address
                    </small>

                    <strong>
                        {{ $member->address_living_in ?: '-' }}
                    </strong>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted d-block">
                        Native Place
                    </small>

                    <strong>
                        {{ $member->native_place ?: '-' }}
                    </strong>

                </div>

            </div>

        </div>

    </div>

    {{-- Family Information --}}
    <div class="card border-0 shadow-sm mb-4 member-profile-section">

        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-house-heart me-2"></i>
                Family Information
            </h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">Family Type</small>
                    <strong>{{ $member->family_type ?: '-' }}</strong>
                </div>

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">Family Status</small>
                    <strong>{{ $member->family_status ?: '-' }}</strong>
                </div>

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">Family Income</small>
                    <strong>{{ $member->family_income ?: '-' }}</strong>
                </div>

                <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Father's Name</small>
                    <strong>{{ $member->father_name ?: '-' }}</strong>
                </div>

                <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Father's Occupation</small>
                    <strong>{{ $member->father_occupation ?: '-' }}</strong>
                </div>

                <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Mother's Name</small>
                    <strong>{{ $member->mother_name ?: '-' }}</strong>
                </div>

                <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Mother's Occupation</small>
                    <strong>{{ $member->mother_occupation ?: '-' }}</strong>
                </div>

                <div class="col-md-3 mb-3">
                    <small class="text-muted d-block">Brothers</small>
                    <strong>{{ $member->no_of_brothers ?: '0' }}</strong>
                </div>

                <div class="col-md-3 mb-3">
                    <small class="text-muted d-block">Married Brothers</small>
                    <strong>{{ $member->married_brothers ?: '0' }}</strong>
                </div>

                <div class="col-md-3 mb-3">
                    <small class="text-muted d-block">Sisters</small>
                    <strong>{{ $member->no_of_sisters ?: '0' }}</strong>
                </div>

                <div class="col-md-3 mb-3">
                    <small class="text-muted d-block">Married Sisters</small>
                    <strong>{{ $member->married_sisters ?: '0' }}</strong>
                </div>

                <div class="col-12">
                    <small class="text-muted d-block">About Family</small>

                    @if(!empty($member->about_family))
                    <p class="mb-0">
                        {{ $member->about_family }}
                    </p>
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </div>

            </div>

        </div>

    </div>

    {{-- Lifestyle --}}
    <div class="card border-0 shadow-sm mb-4 member-profile-section">

        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-person-lines-fill me-2"></i>
                Lifestyle & Health
            </h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-3 mb-3">
                    <small class="text-muted d-block">Diet</small>
                    <strong>{{ $member->diet ?: '-' }}</strong>
                </div>

                <div class="col-md-3 mb-3">
                    <small class="text-muted d-block">Drinking</small>
                    <strong>{{ $member->is_drinking ?: '-' }}</strong>
                </div>

                <div class="col-md-3 mb-3">
                    <small class="text-muted d-block">Smoking</small>
                    <strong>{{ $member->is_smoking ?: '-' }}</strong>
                </div>

                <div class="col-md-3 mb-3">
                    <small class="text-muted d-block">Disability</small>
                    <strong>{{ $member->any_disability ?: '-' }}</strong>
                </div>

                <div class="col-12">
                    <small class="text-muted d-block">Health Information</small>

                    @if(!empty($member->health_info))
                    <p class="mb-0">
                        {{ $member->health_info }}
                    </p>
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </div>

            </div>

        </div>

    </div>
    {{-- Partner Preferences --}}
    <div class="card border-0 shadow-sm mb-4 member-partner-preferences">

        <div class="card-header bg-white border-0 pt-4 px-4">

            <div class="d-flex align-items-center">

                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3"
                    style="width:42px;height:42px;">
                    <i class="bi bi-heart fs-5"></i>
                </div>

                <div>
                    <h5 class="mb-1">Partner Preferences</h5>
                    <small class="text-muted">
                        Preferences for the member's desired partner
                    </small>
                </div>

            </div>

        </div>

        <div class="card-body px-4 pb-4">

            {{-- Basic Preferences --}}
            <h6 class="text-primary border-bottom pb-2 mb-3">
                <i class="bi bi-person-heart me-2"></i>
                Basic Preferences
            </h6>

            <div class="row g-3">

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Looking For
                    </small>
                    <strong>
                        {{ $member->looking_for ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Age From
                    </small>
                    <strong>
                        {{ $member->partner_age_from ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Age To
                    </small>
                    <strong>
                        {{ $member->partner_age_to ?: '-' }}
                    </strong>
                </div>

            </div>


            {{-- Religion & Background --}}
            <h6 class="text-primary border-bottom pb-2 mb-3 mt-5">
                <i class="bi bi-stars me-2"></i>
                Religion & Background
            </h6>

            <div class="row g-3">

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Country
                    </small>
                    <strong>
                        {{ $member->partner_country ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Religion
                    </small>
                    <strong>
                        {{ $member->partner_religion ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Caste
                    </small>
                    <strong>
                        {{ $member->partner_cast ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Mother Tongue
                    </small>
                    <strong>
                        {{ $member->partner_mothertongue ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Manglik
                    </small>
                    <strong>
                        {{ $member->is_partner_manglik ?: '-' }}
                    </strong>
                </div>

            </div>


            {{-- Education & Career --}}
            <h6 class="text-primary border-bottom pb-2 mb-3 mt-5">
                <i class="bi bi-mortarboard me-2"></i>
                Education & Career
            </h6>

            <div class="row g-3">

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Education
                    </small>
                    <strong>
                        {{ $member->partner_education ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Occupation
                    </small>
                    <strong>
                        {{ $member->partner_occupation ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Annual Income From
                    </small>
                    <strong>
                        {{ $member->partner_annual_income_from ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Annual Income To
                    </small>
                    <strong>
                        {{ $member->partner_annual_income_to ?: '-' }}
                    </strong>
                </div>

            </div>


            {{-- Height & Location --}}
            <h6 class="text-primary border-bottom pb-2 mb-3 mt-5">
                <i class="bi bi-geo-alt me-2"></i>
                Height & Location
            </h6>

            <div class="row g-3">

                <div class="col-md-3 mb-3">
                    <small class="text-muted d-block">
                        Height From
                    </small>
                    <strong>
                        {{ $member->partner_height_from ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-3 mb-3">
                    <small class="text-muted d-block">
                        Height To
                    </small>
                    <strong>
                        {{ $member->partner_height_to ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-3 mb-3">
                    <small class="text-muted d-block">
                        State
                    </small>
                    <strong>
                        {{ $member->partner_state ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-3 mb-3">
                    <small class="text-muted d-block">
                        City
                    </small>
                    <strong>
                        {{ $member->partner_city ?: '-' }}
                    </strong>
                </div>

            </div>


            {{-- Lifestyle Preferences --}}
            <h6 class="text-primary border-bottom pb-2 mb-3 mt-5">
                <i class="bi bi-cup-hot me-2"></i>
                Lifestyle Preferences
            </h6>

            <div class="row g-3">

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Diet
                    </small>
                    <strong>
                        {{ $member->partner_diet ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Smoking
                    </small>
                    <strong>
                        {{ $member->is_partner_smoking ?: '-' }}
                    </strong>
                </div>

                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">
                        Drinking
                    </small>
                    <strong>
                        {{ $member->is_partner_drinking ?: '-' }}
                    </strong>
                </div>

            </div>


            {{-- About Partner --}}
            <h6 class="text-primary border-bottom pb-2 mb-3 mt-5">
                <i class="bi bi-chat-heart me-2"></i>
                About Desired Partner
            </h6>

            @if(!empty($member->about_my_partner))

            <p class="mb-0">
                {{ $member->about_my_partner }}
            </p>

            @else

            <p class="text-muted mb-0">
                No information provided.
            </p>

            @endif

        </div>

    </div>
    {{-- =========================================================
    MEMBERSHIP & ACCOUNT INFORMATION
========================================================= --}}

    @php

    $membershipStatus = 'none';
    $membershipStatusLabel = 'No Membership';
    $membershipStatusClass = 'secondary';
    $membershipDaysRemaining = null;

    if ($membershipPlan && $membershipExpiryDate) {

    $today = \Carbon\Carbon::today();

    $membershipDaysRemaining = $today->diffInDays(
    $membershipExpiryDate,
    false
    );

    if ($membershipDaysRemaining < 0) {

        $membershipStatus='expired' ;
        $membershipStatusLabel='Expired' ;
        $membershipStatusClass='danger' ;

        } elseif ($membershipDaysRemaining <=7) {

        $membershipStatus='expiring' ;
        $membershipStatusLabel='Expiring Soon' ;
        $membershipStatusClass='warning' ;

        } else {

        $membershipStatus='active' ;
        $membershipStatusLabel='Active' ;
        $membershipStatusClass='success' ;
        }

        }

        @endphp

        <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="mb-0">
                        Membership & Account Information
                    </h5>

                    <small class="text-muted">
                        Membership, registration and account details
                    </small>
                </div>


                {{-- Change / Assign Plan --}}

                @if($membershipPlan)

                <button
                    type="button"
                    class="btn btn-outline-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#changeMembershipModal">

                    <i class="bi bi-arrow-repeat me-1"></i>
                    Change Plan

                </button>

                @else

                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#changeMembershipModal">

                    <i class="bi bi-plus-circle me-1"></i>
                    Assign Plan

                </button>

                @endif

            </div>

        </div>


        <div class="card-body">

            <div class="row">

                {{-- =================================================
                MEMBER TYPE
            ================================================== --}}

                <div class="col-md-4 mb-4">

                    <small class="text-muted d-block">
                        Member Type
                    </small>

                    <strong>
                        {{ $member->member_type ?: '-' }}
                    </strong>

                </div>


                {{-- =================================================
                MEMBERSHIP PLAN
            ================================================== --}}

                <div class="col-md-4 mb-4">

                    <small class="text-muted d-block">
                        Membership Plan
                    </small>


                    @if($membershipPlan)

                    <strong>
                        {{ $membershipPlan->plan_name }}
                    </strong>

                    @if(!empty($membershipPlan->membership_type))

                    <span class="badge bg-primary-subtle text-primary ms-1">
                        {{ $membershipPlan->membership_type }}
                    </span>

                    @endif

                    @else

                    <span class="text-muted">
                        No Plan

                    </span>

                    @endif

                </div>
                {{-- =================================================
    MEMBERSHIP STATUS
================================================== --}}

                <div class="col-md-4 mb-4">

                    <small class="text-muted d-block">
                        Membership Status
                    </small>

                    @if($membershipStatus === 'active')

                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Active
                    </span>

                    @elseif($membershipStatus === 'expiring')

                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Expiring Soon
                    </span>

                    @if($membershipDaysRemaining !== null)

                    <small class="text-muted ms-1">
                        {{ $membershipDaysRemaining }}
                        {{ $membershipDaysRemaining == 1 ? 'day' : 'days' }}
                        left
                    </small>

                    @endif

                    @elseif($membershipStatus === 'expired')

                    <span class="badge bg-danger">
                        <i class="bi bi-x-circle me-1"></i>
                        Expired
                    </span>

                    @else

                    <span class="badge bg-secondary">
                        <i class="bi bi-dash-circle me-1"></i>
                        No Membership
                    </span>

                    @endif

                </div>


                {{-- =================================================
                PLAN ACTIVATION DATE
            ================================================== --}}

                <div class="col-md-4 mb-4">

                    <small class="text-muted d-block">
                        Plan Activation Date
                    </small>

                    <strong>

                        @if(!empty($member->plan_activation_date))

                        {{ \Carbon\Carbon::parse($member->plan_activation_date)->format('d-m-Y') }}

                        @else

                        -

                        @endif

                    </strong>

                </div>


                {{-- =================================================
                PLAN EXPIRY DATE
            ================================================== --}}

                <div class="col-md-4 mb-4">

                    <small class="text-muted d-block">
                        Plan Expiry Date
                    </small>

                    @if($membershipExpiryDate)

                    @php
                    $today = \Carbon\Carbon::today();

                    $daysRemaining = $today->diffInDays(
                    $membershipExpiryDate,
                    false
                    );
                    @endphp


                    <strong>

                        {{ $membershipExpiryDate->format('d-m-Y') }}

                    </strong>


                    @if($daysRemaining < 0)

                        <span class="badge bg-danger ms-1">
                        Expired
                        </span>

                        @elseif($daysRemaining <= 7)

                            <span class="badge bg-warning text-dark ms-1">
                            {{ $daysRemaining }} days left
                            </span>

                            @else

                            <span class="badge bg-success ms-1">
                                Active
                            </span>

                            @endif

                            @else

                            <span class="text-muted">
                                -
                            </span>

                            @endif

                </div>


                {{-- =================================================
                PLAN DURATION
            ================================================== --}}

                <div class="col-md-4 mb-4">

                    <small class="text-muted d-block">
                        Plan Duration
                    </small>

                    <strong>

                        @if($membershipPlan)

                        {{ $membershipPlan->duration_days }}

                        {{ $membershipPlan->duration_days == 1 ? 'Day' : 'Days' }}

                        @else

                        -

                        @endif

                    </strong>

                </div>


                {{-- =================================================
                PROFILE VIEWS
            ================================================== --}}

                <div class="col-md-4 mb-4">

                    <small class="text-muted d-block">
                        Profile Views Allowed
                    </small>

                    <strong>

                        {{ $membershipPlan->view_profile ?? '-' }}

                    </strong>

                </div>

                {{-- =========================================================
    MEMBERSHIP USAGE
========================================================= --}}

                @if($membershipPlan)

                <div class="col-12 mt-2">

                    <hr>

                    <h6 class="mb-3">
                        <i class="bi bi-bar-chart me-1"></i>
                        Membership Usage
                    </h6>

                </div>


                {{-- =====================================================
        PROFILE VIEWS USAGE
    ====================================================== --}}

                <div class="col-md-6 mb-3">

                    <div class="border rounded-3 p-3">

                        <div class="d-flex justify-content-between align-items-center mb-2">

                            <div>

                                <small class="text-muted d-block">
                                    Profile Views
                                </small>

                                <strong>
                                    {{ $profileViewsUsed }}
                                    /
                                    {{ $profileViewsAllowed }}
                                </strong>

                            </div>

                            <div class="text-end">

                                <small class="text-muted d-block">
                                    Remaining
                                </small>

                                <strong
                                    class="{{ $profileViewsRemaining > 0
                            ? 'text-success'
                            : 'text-danger' }}">

                                    {{ $profileViewsRemaining }}

                                </strong>

                            </div>

                        </div>


                        @php
                        $profilePercentage = $profileViewsAllowed > 0
                        ? min(
                        100,
                        round(
                        ($profileViewsUsed / $profileViewsAllowed) * 100
                        )
                        )
                        : 0;
                        @endphp


                        <div
                            class="progress"
                            style="height:8px;">

                            <div
                                class="progress-bar
                        {{ $profilePercentage >= 100
                            ? 'bg-danger'
                            : ($profilePercentage >= 80
                                ? 'bg-warning'
                                : 'bg-success') }}"
                                role="progressbar"
                                style="width: {{ $profilePercentage }}%;"
                                aria-valuenow="{{ $profilePercentage }}"
                                aria-valuemin="0"
                                aria-valuemax="100">
                            </div>

                        </div>


                        <small class="text-muted mt-2 d-block">

                            {{ $profilePercentage }}% used

                        </small>

                    </div>

                </div>


                {{-- =====================================================
        CONTACT VIEWS USAGE
    ====================================================== --}}

                <div class="col-md-6 mb-3">

                    <div class="border rounded-3 p-3">

                        <div class="d-flex justify-content-between align-items-center mb-2">

                            <div>

                                <small class="text-muted d-block">
                                    Contact Views
                                </small>

                                <strong>
                                    {{ $contactViewsUsed }}
                                    /
                                    {{ $contactViewsAllowed }}
                                </strong>

                            </div>

                            <div class="text-end">

                                <small class="text-muted d-block">
                                    Remaining
                                </small>

                                <strong
                                    class="{{ $contactViewsRemaining > 0
                            ? 'text-success'
                            : 'text-danger' }}">

                                    {{ $contactViewsRemaining }}

                                </strong>

                            </div>

                        </div>


                        @php
                        $contactPercentage = $contactViewsAllowed > 0
                        ? min(
                        100,
                        round(
                        ($contactViewsUsed / $contactViewsAllowed) * 100
                        )
                        )
                        : 0;
                        @endphp


                        <div
                            class="progress"
                            style="height:8px;">

                            <div
                                class="progress-bar
                        {{ $contactPercentage >= 100
                            ? 'bg-danger'
                            : ($contactPercentage >= 80
                                ? 'bg-warning'
                                : 'bg-success') }}"
                                role="progressbar"
                                style="width: {{ $contactPercentage }}%;"
                                aria-valuenow="{{ $contactPercentage }}"
                                aria-valuemin="0"
                                aria-valuemax="100">
                            </div>

                        </div>


                        <small class="text-muted mt-2 d-block">

                            {{ $contactPercentage }}% used

                        </small>

                    </div>

                </div>

                @endif


                {{-- =================================================
                CONTACT VIEWS
            ================================================== --}}

                <div class="col-md-4 mb-4">

                    <small class="text-muted d-block">
                        Contact Views Allowed
                    </small>

                    <strong>

                        {{ $membershipPlan->view_contact ?? '-' }}

                    </strong>

                </div>


                {{-- =================================================
                PLAN COST
            ================================================== --}}

                <div class="col-md-4 mb-4">

                    <small class="text-muted d-block">
                        Plan Cost
                    </small>

                    <strong>

                        @if($membershipPlan)

                        ₹{{ number_format(
                            (float) $membershipPlan->final_cost,
                            2
                        ) }}

                        @else

                        -

                        @endif

                    </strong>

                </div>


                {{-- =================================================
                REGISTRATION THROUGH
            ================================================== --}}

                <div class="col-md-4 mb-4">

                    <small class="text-muted d-block">
                        Registration Through
                    </small>

                    <strong>
                        {{ $member->register_through ?: '-' }}
                    </strong>

                </div>


                {{-- =================================================
                REGISTRATION DATE
            ================================================== --}}

                <div class="col-md-4 mb-4">

                    <small class="text-muted d-block">
                        Registration Date
                    </small>

                    <strong>
                        {{ $member->registration_date ?: '-' }}
                    </strong>

                </div>


                {{-- =================================================
                ACTIVATION NUMBER
            ================================================== --}}

                <div class="col-md-4 mb-4">

                    <small class="text-muted d-block">
                        Activation Number
                    </small>

                    <strong>
                        {{ $member->activation_number ?: '0' }}
                    </strong>

                </div>

            </div>

        </div>
</div>

{{-- =========================================================
    ACCOUNT STATUS
========================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="mb-0">
                    <i class="bi bi-shield-check me-2"></i>
                    Account Status
                </h5>

                <small class="text-muted">
                    Profile and account visibility information
                </small>

            </div>

        </div>

    </div>


    <div class="card-body">

        <div class="row">


            {{-- =================================================
                ACCOUNT STATUS
            ================================================== --}}

            <div class="col-md-4 mb-4">

                <small class="text-muted d-block mb-1">
                    Account Status
                </small>

                @if($member->active === 'Yes')

                <span class="badge bg-success">
                    <i class="bi bi-check-circle me-1"></i>
                    Active
                </span>

                @elseif($member->active === 'Banned')

                <span class="badge bg-danger">
                    <i class="bi bi-slash-circle me-1"></i>
                    Banned
                </span>

                @elseif($member->active === 'deleted')

                <span class="badge bg-dark">
                    <i class="bi bi-trash me-1"></i>
                    Deleted
                </span>

                @else

                <span class="badge bg-secondary">
                    <i class="bi bi-pause-circle me-1"></i>
                    Inactive
                </span>

                @endif

            </div>


            {{-- =================================================
                PRE ACTIVE
            ================================================== --}}

            <div class="col-md-4 mb-4">

                <small class="text-muted d-block mb-1">
                    Pre-Active
                </small>

                @if($member->pre_active === 'Yes')

                <span class="badge bg-warning text-dark">
                    Yes
                </span>

                @else

                <span class="badge bg-secondary">
                    No
                </span>

                @endif

            </div>


            {{-- =================================================
                PROFILE VISIBILITY
            ================================================== --}}

            <div class="col-md-4 mb-4">

                <small class="text-muted d-block mb-1">
                    Profile Visibility
                </small>

                @if((string) $member->profile_hide === '1')

                <span class="badge bg-warning text-dark">
                    <i class="bi bi-eye-slash me-1"></i>
                    Hidden
                </span>

                @else

                <span class="badge bg-success">
                    <i class="bi bi-eye me-1"></i>
                    Visible
                </span>

                @endif

            </div>


            {{-- =================================================
                TRUSTED
            ================================================== --}}

            <div class="col-md-4 mb-4">

                <small class="text-muted d-block mb-1">
                    Trusted Profile
                </small>

                @if($member->is_trusted === 'Yes')

                <span class="badge bg-success">
                    <i class="bi bi-patch-check-fill me-1"></i>
                    Trusted
                </span>

                @else

                <span class="badge bg-secondary">
                    Not Trusted
                </span>

                @endif

            </div>


            {{-- =================================================
                PROMOTED
            ================================================== --}}

            <div class="col-md-4 mb-4">

                <small class="text-muted d-block mb-1">
                    Promoted
                </small>

                @if($member->promoted === 'Yes')

                <span class="badge bg-primary">
                    <i class="bi bi-megaphone-fill me-1"></i>
                    Promoted
                </span>

                @else

                <span class="badge bg-secondary">
                    Not Promoted
                </span>

                @endif

            </div>


            {{-- =================================================
                ACTIVATION NUMBER
            ================================================== --}}

            <div class="col-md-4 mb-4">

                <small class="text-muted d-block mb-1">
                    Activation Number
                </small>

                <strong>
                    {{ $member->activation_number ?: '0' }}
                </strong>

            </div>

        </div>

    </div>

</div>

{{-- =========================================================
    MEMBERSHIP PAYMENT HISTORY
========================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="mb-0">
                    <i class="bi bi-credit-card me-2"></i>
                    Payment History
                </h5>

                <small class="text-muted">
                    Recent membership payments
                </small>

            </div>

            <span class="badge bg-light text-dark">
                {{ $membershipPayments->count() }}
                {{ $membershipPayments->count() == 1 ? 'Payment' : 'Payments' }}
            </span>

        </div>

    </div>


    <div class="card-body p-0">

        @if($membershipPayments->count())

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th class="px-4">
                            Date
                        </th>

                        <th>
                            Plan
                        </th>

                        <th>
                            Payment ID
                        </th>

                        <th>
                            Amount
                        </th>

                        <th>
                            Remarks
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($membershipPayments as $payment)

                    <tr>

                        <td class="px-4">

                            @if($payment->payment_date)

                            {{ \Carbon\Carbon::parse(
                                            $payment->payment_date
                                        )->format('d-m-Y h:i A') }}

                            @else

                            —

                            @endif

                        </td>


                        <td>

                            @if($payment->plan_name)

                            <span class="fw-semibold">
                                {{ $payment->plan_name }}
                            </span>

                            @else

                            <span class="text-muted">
                                Plan #{{ $payment->plan_id }}
                            </span>

                            @endif

                        </td>


                        <td>

                            <code>
                                {{ $payment->payment_id ?: '—' }}
                            </code>

                        </td>


                        <td>

                            <strong>
                                ₹{{ number_format(
                                            (float) $payment->amount,
                                            2
                                        ) }}
                            </strong>

                        </td>


                        <td>

                            @if($payment->remarks)

                            {{ $payment->remarks }}

                            @else

                            <span class="text-muted">
                                —
                            </span>

                            @endif

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        @else

        <div class="text-center py-5">

            <i class="bi bi-receipt fs-1 text-muted"></i>

            <h6 class="mt-3 mb-1">
                No Payment History
            </h6>

            <p class="text-muted mb-0">
                No membership payments have been recorded for this member.
            </p>

        </div>

        @endif

    </div>

</div>

{{-- =========================================================
    ACTIVITY OVERVIEW
========================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="mb-0">
                    <i class="bi bi-activity me-2"></i>
                    Activity Overview
                </h5>

                <small class="text-muted">
                    Member activity summary
                </small>

            </div>


            <a
                href="{{ route('admin.activities.member', [
                    'memberId' => $member->id,
                    'activity' => 'profile-views'
                ]) }}"
                class="btn btn-outline-primary btn-sm">

                <i class="bi bi-list-ul me-1"></i>
                View Activity

            </a>

        </div>

    </div>


    <div class="card-body">

        <div class="row g-3">


            {{-- =================================================
                SHORTLISTED
            ================================================== --}}

            <div class="col-6 col-md-4 col-lg-2">

                <a
                    href="{{ route('admin.activities.member', [
                        'memberId' => $member->id,
                        'activity' => 'shortlisted'
                    ]) }}"
                    class="text-decoration-none">

                    <div class="border rounded-3 p-3 h-100 text-center">

                        <div
                            class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mx-auto mb-2"
                            style="width:44px;height:44px;">

                            <i class="bi bi-bookmark fs-5"></i>

                        </div>

                        <small class="text-muted d-block">
                            Shortlisted
                        </small>

                        <h5 class="mb-0 mt-1 text-dark">
                            {{ $activityCounts['shortlisted'] }}
                        </h5>

                    </div>

                </a>

            </div>


            {{-- =================================================
                SENT INTERESTS
            ================================================== --}}

            <div class="col-6 col-md-4 col-lg-2">

                <a
                    href="{{ route('admin.activities.member', [
                        'memberId' => $member->id,
                        'activity' => 'sent-interests'
                    ]) }}"
                    class="text-decoration-none">

                    <div class="border rounded-3 p-3 h-100 text-center">

                        <div
                            class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center mx-auto mb-2"
                            style="width:44px;height:44px;">

                            <i class="bi bi-send fs-5"></i>

                        </div>

                        <small class="text-muted d-block">
                            Sent Interests
                        </small>

                        <h5 class="mb-0 mt-1 text-dark">
                            {{ $activityCounts['sent_interests'] }}
                        </h5>

                    </div>

                </a>

            </div>


            {{-- =================================================
                RECEIVED INTERESTS
            ================================================== --}}

            <div class="col-6 col-md-4 col-lg-2">

                <a
                    href="{{ route('admin.activities.member', [
                        'memberId' => $member->id,
                        'activity' => 'received-interests'
                    ]) }}"
                    class="text-decoration-none">

                    <div class="border rounded-3 p-3 h-100 text-center">

                        <div
                            class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center mx-auto mb-2"
                            style="width:44px;height:44px;">

                            <i class="bi bi-inbox fs-5"></i>

                        </div>

                        <small class="text-muted d-block">
                            Received
                        </small>

                        <h5 class="mb-0 mt-1 text-dark">
                            {{ $activityCounts['received_interests'] }}
                        </h5>

                    </div>

                </a>

            </div>


            {{-- =================================================
                PROFILE VIEWS
            ================================================== --}}

            <div class="col-6 col-md-4 col-lg-2">

                <a
                    href="{{ route('admin.activities.member', [
                        'memberId' => $member->id,
                        'activity' => 'profile-views'
                    ]) }}"
                    class="text-decoration-none">

                    <div class="border rounded-3 p-3 h-100 text-center">

                        <div
                            class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center mx-auto mb-2"
                            style="width:44px;height:44px;">

                            <i class="bi bi-eye fs-5"></i>

                        </div>

                        <small class="text-muted d-block">
                            Profile Views
                        </small>

                        <h5 class="mb-0 mt-1 text-dark">
                            {{ $activityCounts['profile_views'] }}
                        </h5>

                    </div>

                </a>

            </div>


            {{-- =================================================
                CONTACT VIEWS
            ================================================== --}}

            <div class="col-6 col-md-4 col-lg-2">

                <a
                    href="{{ route('admin.activities.member', [
                        'memberId' => $member->id,
                        'activity' => 'contact-views'
                    ]) }}"
                    class="text-decoration-none">

                    <div class="border rounded-3 p-3 h-100 text-center">

                        <div
                            class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mx-auto mb-2"
                            style="width:44px;height:44px;">

                            <i class="bi bi-person-lines-fill fs-5"></i>

                        </div>

                        <small class="text-muted d-block">
                            Contact Views
                        </small>

                        <h5 class="mb-0 mt-1 text-dark">
                            {{ $activityCounts['contact_views'] }}
                        </h5>

                    </div>

                </a>

            </div>


            {{-- =================================================
                PAYMENTS
            ================================================== --}}

            <div class="col-6 col-md-4 col-lg-2">

                <a
                    href="{{ route('admin.activities.member', [
                        'memberId' => $member->id,
                        'activity' => 'wallet-payments'
                    ]) }}"
                    class="text-decoration-none">

                    <div class="border rounded-3 p-3 h-100 text-center">

                        <div
                            class="rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center mx-auto mb-2"
                            style="width:44px;height:44px;">

                            <i class="bi bi-wallet2 fs-5"></i>

                        </div>

                        <small class="text-muted d-block">
                            Payments
                        </small>

                        <h5 class="mb-0 mt-1 text-dark">
                            {{ $activityCounts['wallet_payments'] }}
                        </h5>

                    </div>

                </a>

            </div>

        </div>

    </div>

</div>

{{-- =========================================================
    RELATIONSHIP MANAGER
========================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="mb-0">
                    <i class="bi bi-person-badge me-2"></i>
                    Relationship Manager
                </h5>

                <small class="text-muted">
                    Internal member relationship management
                </small>

            </div>

        </div>

    </div>


    <div class="card-body">
        <div class="mb-3">

            <small class="text-muted d-block mb-1">
                Current Relationship Manager
            </small>

            @if(!empty($member->relationship_manager))

            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                <i class="bi bi-person-badge me-1"></i>
                {{ $member->relationship_manager }}
            </span>

            @else

            <span class="text-muted">
                Not Assigned
            </span>

            @endif

        </div>
        <form
            action="{{ route('admin.members.relationship-manager.update', [
                'memberId' => $member->id
            ]) }}"
            method="POST">

            @csrf


            <div class="row align-items-end">

                {{-- =================================================
                    CURRENT RELATIONSHIP MANAGER
                ================================================== --}}

                <div class="col-md-8">

                    <label
                        for="relationship_manager"
                        class="form-label">

                        Change Relationship Manager

                    </label>

                    <select
                        name="relationship_manager"
                        id="relationship_manager"
                        class="form-select @error('relationship_manager') is-invalid @enderror">

                        <option value="">
                            — No Relationship Manager —
                        </option>

                        @foreach($relationshipManagers as $manager)

                        <option
                            value="{{ $manager->display_name }}"
                            {{ old(
                'relationship_manager',
                $member->relationship_manager
            ) === $manager->display_name ? 'selected' : '' }}>

                            {{ $manager->display_name }}

                        </option>

                        @endforeach

                    </select>


                    @error('relationship_manager')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                    @enderror

                </div>


                {{-- =================================================
                    SAVE
                ================================================== --}}

                <div class="col-md-4 mt-3 mt-md-0">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-check-circle me-1"></i>
                        Save Relationship Manager

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

{{-- =========================================================
    ADMIN REMARKS
========================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <div class="d-flex align-items-center">

            <div
                class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-3"
                style="width:42px;height:42px;">

                <i class="bi bi-sticky fs-5"></i>

            </div>

            <div>

                <h5 class="mb-0">
                    Admin Remarks
                </h5>

                <small class="text-muted">
                    Internal notes about this member
                </small>

            </div>

        </div>

    </div>


    <div class="card-body">

        <form
            action="{{ route('admin.members.remarks.update', [
                'memberId' => $member->id
            ]) }}"
            method="POST">

            @csrf


            <div class="mb-3">

                <label
                    for="remarks"
                    class="form-label fw-semibold">

                    Remarks

                </label>

                <textarea
                    name="remarks"
                    id="remarks"
                    rows="5"
                    class="form-control @error('remarks') is-invalid @enderror"
                    placeholder="Enter internal remarks about this member...">{{ old('remarks', $member->remarks) }}</textarea>


                @error('remarks')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

                @enderror

            </div>


            <div class="d-flex justify-content-between align-items-center">

                <small class="text-muted">
                    These remarks are for admin use only.
                </small>


                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="bi bi-check-circle me-1"></i>
                    Save Remarks

                </button>

            </div>

        </form>

    </div>

</div>

{{-- Photos & Gallery --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Photos & Gallery</h5>
    </div>

    <div class="card-body">

        {{-- Main Profile Photo --}}
        <div class="mb-4">

            <h6 class="fw-semibold mb-3">
                Profile Photo
            </h6>

            <div class="row">

                <div class="col-md-3">

                    @if(!empty($member->photo))

                    <div class="border rounded-3 overflow-hidden bg-light">

                        <img
                            src="{{ $member->photo_url }}"
                            alt="{{ $member->full_name }}"
                            class="img-fluid w-100"
                            style="height: 220px; object-fit: cover;"
                            loading="lazy"
                            decoding="async">

                    </div>

                    @else

                    <div
                        class="border rounded-3 bg-light d-flex align-items-center justify-content-center"
                        style="height: 220px;">
                        <span class="text-muted">
                            No profile photo
                        </span>
                    </div>

                    @endif

                </div>

                <div class="col-md-9">

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Photo
                            </small>

                            <strong>
                                {{ $member->photo ?: 'Not uploaded' }}
                            </strong>

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Approval
                            </small>

                            @if(strtolower((string) $member->photo_approved) === 'yes')

                            <span class="badge bg-success">
                                Approved
                            </span>

                            @else

                            <span class="badge bg-secondary">
                                {{ $member->photo_approved ?: 'Not Approved' }}
                            </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <hr class="my-4">


        {{-- Gallery --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <h5 class="mb-4">
                    <i class="bi bi-images me-2"></i>
                    Gallery
                </h5>


                @if($galleryPhotos->count())

                <div class="row g-3">

                    @foreach($galleryPhotos as $photo)

                    <div class="col-6 col-md-4 col-lg-3">

                        <div class="card border-0 shadow-sm h-100">


                            {{-- =================================================
                                PHOTO
                            ================================================== --}}

                            <div class="position-relative">

                                <img
                                    src="{{ $photo->photo_url }}"
                                    alt="{{ $member->full_name }} photo"
                                    class="card-img-top"
                                    style="height:180px; object-fit:cover;"
                                    loading="lazy"
                                    decoding="async">


                                {{-- Current Profile Photo Badge --}}
                                @if($member->photo === $photo->photo)

                                <span
                                    class="position-absolute top-0 end-0 m-2 badge bg-success">

                                    <i class="bi bi-person-check me-1"></i>
                                    Profile

                                </span>

                                @endif

                            </div>


                            {{-- =================================================
                                PHOTO INFORMATION
                            ================================================== --}}

                            <div class="card-body p-3">


                                {{-- Photo ID --}}
                                <div class="d-flex justify-content-between align-items-center mb-2">

                                    <small class="text-muted">

                                        Photo #{{ $photo->id }}

                                    </small>


                                    {{-- Approval Status --}}
                                    @if($photo->photo_approved === 'Yes')

                                    <span class="badge bg-success">

                                        <i class="bi bi-check-circle me-1"></i>
                                        Approved

                                    </span>

                                    @else

                                    <span class="badge bg-warning text-dark">

                                        <i class="bi bi-clock me-1"></i>
                                        Pending

                                    </span>

                                    @endif

                                </div>


                                {{-- =================================================
                                    PRIVACY
                                ================================================== --}}

                                <div class="mb-3">

                                    @if($photo->photo_privacy == 1)

                                    <small class="text-muted">

                                        <i class="bi bi-globe me-1"></i>
                                        Public

                                    </small>

                                    @else

                                    <small class="text-muted">

                                        <i class="bi bi-lock me-1"></i>
                                        Private

                                    </small>

                                    @endif

                                </div>


                                {{-- =================================================
                                    APPROVE / REJECT
                                ================================================== --}}

                                @if($photo->photo_approved === 'Yes')

                                {{-- Reject Photo --}}

                                <form
                                    action="{{ route('admin.members.photos.unapprove', [
                                            'memberId' => $member->id,
                                            'photoId' => $photo->id
                                        ]) }}"
                                    method="POST"
                                    class="mb-2">

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger w-100"
                                        onclick="return confirm('Are you sure you want to reject this photo?')">

                                        <i class="bi bi-x-circle me-1"></i>
                                        Reject Photo

                                    </button>

                                </form>

                                @else

                                {{-- Approve Photo --}}

                                <form
                                    action="{{ route('admin.members.photos.approve', [
                                            'memberId' => $member->id,
                                            'photoId' => $photo->id
                                        ]) }}"
                                    method="POST"
                                    class="mb-2">

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-success w-100"
                                        onclick="return confirm('Are you sure you want to approve this photo?')">

                                        <i class="bi bi-check-circle me-1"></i>
                                        Approve Photo

                                    </button>

                                </form>

                                @endif


                                {{-- =================================================
                                    SET AS PROFILE PHOTO
                                ================================================== --}}

                                @if($member->photo === $photo->photo)

                                <button
                                    type="button"
                                    class="btn btn-sm btn-success w-100"
                                    disabled>

                                    <i class="bi bi-person-check me-1"></i>
                                    Current Profile Photo

                                </button>

                                @else

                                <form
                                    action="{{ route('admin.members.photos.set-profile', [
                                            'memberId' => $member->id,
                                            'photoId' => $photo->id
                                        ]) }}"
                                    method="POST">

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-primary w-100"
                                        onclick="return confirm('Set this photo as the profile photo?')">

                                        <i class="bi bi-person-check me-1"></i>
                                        Set as Profile

                                    </button>

                                </form>

                                @endif

                                {{-- =================================================
    DELETE PHOTO
================================================== --}}

                                @if($member->photo !== $photo->photo)

                                <form
                                    action="{{ route('admin.members.photos.destroy', [
            'memberId' => $member->id,
            'photoId' => $photo->id
        ]) }}"
                                    method="POST"
                                    class="mt-2">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger w-100"
                                        onclick="return confirm('Are you sure you want to permanently delete this photo?')">

                                        <i class="bi bi-trash me-1"></i>
                                        Delete Photo

                                    </button>

                                </form>

                                @else

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary w-100 mt-2"
                                    disabled>

                                    <i class="bi bi-lock me-1"></i>
                                    Cannot Delete Profile Photo

                                </button>

                                @endif


                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>


                @else

                {{-- =================================================
                NO PHOTOS
            ================================================== --}}

                <div class="text-center py-5 text-muted">

                    <i class="bi bi-images fs-1"></i>

                    <h6 class="mt-3">
                        No gallery photos found
                    </h6>

                    <p class="mb-0">
                        This member has not uploaded any gallery photos.
                    </p>

                </div>

                @endif

            </div>

        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">

            {{-- Activate / Deactivate --}}
            @if($member->is_active)

            <form method="POST"
                action="{{ route('admin.members.toggle-status', $member->id) }}">
                @csrf

                <button type="submit" class="btn btn-warning">
                    Deactivate
                </button>
            </form>

            @else

            <form method="POST"
                action="{{ route('admin.members.toggle-status', $member->id) }}">
                @csrf

                <button type="submit" class="btn btn-success">
                    Activate
                </button>
            </form>

            @endif


            {{-- Trust --}}
            <form method="POST"
                action="{{ route('admin.members.toggle-trusted', $member->id) }}">
                @csrf

                <button type="submit" class="btn btn-outline-primary">

                    @if(strtolower((string) $member->is_trusted) === 'yes')
                    Remove Trusted
                    @else
                    Mark Trusted
                    @endif

                </button>
            </form>


            {{-- Hide / Show --}}
            <form method="POST"
                action="{{ route('admin.members.toggle-visibility', $member->id) }}">
                @csrf

                <button type="submit" class="btn btn-outline-secondary">

                    @if(!empty($member->profile_hide) &&
                    strtolower((string) $member->profile_hide) === 'yes')

                    Show Profile

                    @else

                    Hide Profile

                    @endif

                </button>
            </form>


            {{-- Promote --}}
            <form method="POST"
                action="{{ route('admin.members.toggle-promoted', $member->id) }}">
                @csrf

                <button type="submit" class="btn btn-outline-success">

                    @if(strtolower((string) $member->promoted) === 'yes')
                    Remove Promotion
                    @else
                    Promote Profile
                    @endif

                </button>
            </form>

            @if(auth('admin')->user()?->hasPermission('raise-profile-delete-request'))

            <button
                type="button"
                class="btn btn-outline-danger"
                data-bs-toggle="modal"
                data-bs-target="#deleteRequestModal"
                data-action="{{ route('admin.members.delete-request', $member->id) }}"
                data-member-id="{{ $member->id }}"
                data-profile-id="{{ $member->profile_id }}"
                data-member-name="{{ $member->full_name }}">
                <i class="bi bi-trash3 me-1"></i>
                Raise Delete Request
            </button>

            @endif

        </div>

    </div>

    <div
        class="modal fade"
        id="shareProfileModal"
        tabindex="-1"
        aria-labelledby="shareProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="shareProfileModalLabel">
                            Share {{ $member->full_name }}'s Profile
                        </h5>
                        <div class="small text-muted mt-1">
                            This secure link expires in 7 days and hides contact details.
                        </div>
                    </div>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <label for="sharedProfileUrl" class="form-label">
                        Shareable profile link
                    </label>

                    <div class="input-group">
                        <input
                            type="text"
                            id="sharedProfileUrl"
                            class="form-control"
                            value="{{ $shareUrl }}"
                            readonly>
                        <button
                            type="button"
                            class="btn btn-outline-primary"
                            id="copySharedProfileUrl">
                            <i class="bi bi-copy me-1"></i>
                            <span>Copy</span>
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <a
                        href="{{ $whatsappShareUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-success">
                        <i class="bi bi-whatsapp me-1"></i>
                        Share on WhatsApp
                    </a>
                    <a
                        href="{{ $shareUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-outline-secondary">
                        Preview
                    </a>
                </div>
            </div>
        </div>
    </div>

    @endsection

    {{-- =========================================================
    CHANGE MEMBERSHIP MODAL
========================================================= --}}

    <div
        class="modal fade"
        id="changeMembershipModal"
        tabindex="-1"
        aria-labelledby="changeMembershipModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content border-0 shadow">

                {{-- Header --}}

                <div class="modal-header">

                    <div>

                        <h5
                            class="modal-title"
                            id="changeMembershipModalLabel">

                            <i class="bi bi-award me-2"></i>
                            Change Membership

                        </h5>

                        <small class="text-muted">
                            Select a membership plan for
                            {{ $member->full_name }}
                        </small>

                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                {{-- Form --}}

                <form
                    action="{{ route('admin.members.membership.change', [
                    'memberId' => $member->id
                ]) }}"
                    method="POST">

                    @csrf


                    <div class="modal-body">

                        {{-- Plan --}}

                        <div class="mb-4">

                            <label
                                for="membership_plan_id"
                                class="form-label fw-semibold">

                                Membership Plan

                            </label>


                            <select
                                name="plan_id"
                                id="membership_plan_id"
                                class="form-select"
                                required>

                                <option value="">
                                    Select a plan
                                </option>

                                @foreach($plans as $plan)

                                <option
                                    value="{{ $plan->id }}"
                                    data-duration="{{ $plan->duration_days }}"
                                    data-profile-views="{{ $plan->view_profile }}"
                                    data-contact-views="{{ $plan->view_contact }}"
                                    data-cost="{{ $plan->final_cost }}">

                                    {{ $plan->plan_name }}

                                    @if(!empty($plan->membership_type))
                                    — {{ $plan->membership_type }}
                                    @endif

                                    — ₹{{ number_format((float) $plan->final_cost, 2) }}

                                </option>

                                @endforeach

                            </select>


                            @error('plan_id')

                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>

                            @enderror

                        </div>


                        {{-- Plan Details --}}

                        <div
                            id="selectedPlanDetails"
                            class="border rounded-3 p-3 bg-light d-none">

                            <h6 class="mb-3">
                                Selected Plan
                            </h6>


                            <div class="row g-3">

                                <div class="col-md-3">

                                    <small class="text-muted d-block">
                                        Duration
                                    </small>

                                    <strong id="planDuration">
                                        —
                                    </strong>

                                </div>


                                <div class="col-md-3">

                                    <small class="text-muted d-block">
                                        Profile Views
                                    </small>

                                    <strong id="planProfileViews">
                                        —
                                    </strong>

                                </div>


                                <div class="col-md-3">

                                    <small class="text-muted d-block">
                                        Contact Views
                                    </small>

                                    <strong id="planContactViews">
                                        —
                                    </strong>

                                </div>


                                <div class="col-md-3">

                                    <small class="text-muted d-block">
                                        Cost
                                    </small>

                                    <strong id="planCost">
                                        —
                                    </strong>

                                </div>

                            </div>

                        </div>


                        {{-- Activation Date --}}

                        <div class="mt-4">

                            <label
                                for="plan_activation_date"
                                class="form-label fw-semibold">

                                Activation Date

                            </label>


                            <input
                                type="date"
                                name="plan_activation_date"
                                id="plan_activation_date"
                                class="form-control"
                                value="{{ $member->plan_activation_date ?: now()->format('Y-m-d') }}"
                                required>


                            @error('plan_activation_date')

                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>

                            @enderror

                        </div>


                        {{-- Expiry Preview --}}

                        <div
                            id="membershipExpiryPreview"
                            class="alert alert-info mt-4 d-none">

                            <i class="bi bi-calendar-check me-2"></i>

                            Membership expiry:

                            <strong id="membershipExpiryDate">
                                —
                            </strong>

                        </div>

                    </div>


                    {{-- Footer --}}

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">

                            Cancel

                        </button>


                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-check-circle me-1"></i>

                            Save Membership

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    @push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const input = document.getElementById('member_photo');

            const previewContainer =
                document.getElementById('photoPreviewContainer');

            const preview =
                document.getElementById('photoPreview');


            if (!input) {
                return;
            }


            input.addEventListener('change', function() {

                const file = this.files[0];

                if (!file) {

                    previewContainer.classList.add('d-none');

                    preview.src = '';

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Client-side validation
                |--------------------------------------------------------------------------
                */

                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];


                if (!allowedTypes.includes(file.type)) {

                    alert(
                        'Please select a JPG, JPEG, PNG or WebP image.'
                    );

                    this.value = '';

                    previewContainer.classList.add('d-none');

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | 10 MB limit
                |--------------------------------------------------------------------------
                */

                if (file.size > 10 * 1024 * 1024) {

                    alert(
                        'The selected image cannot be larger than 10 MB.'
                    );

                    this.value = '';

                    previewContainer.classList.add('d-none');

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Preview
                |--------------------------------------------------------------------------
                */

                const reader = new FileReader();

                reader.onload = function(event) {

                    preview.src = event.target.result;

                    previewContainer.classList.remove('d-none');

                };

                reader.readAsDataURL(file);

            });

        });
    </script>

    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const copyButton = document.getElementById('copySharedProfileUrl');
            const shareInput = document.getElementById('sharedProfileUrl');

            if (!copyButton || !shareInput) {
                return;
            }

            copyButton.addEventListener('click', async function() {
                try {
                    await navigator.clipboard.writeText(shareInput.value);
                } catch (error) {
                    shareInput.select();
                    document.execCommand('copy');
                }

                const label = copyButton.querySelector('span');
                label.textContent = 'Copied';

                setTimeout(function() {
                    label.textContent = 'Copy';
                }, 1800);
            });
        });
    </script>
    @endpush

    @push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const planSelect =
                document.getElementById('membership_plan_id');

            const activationDate =
                document.getElementById('plan_activation_date');

            const details =
                document.getElementById('selectedPlanDetails');

            const expiryPreview =
                document.getElementById('membershipExpiryPreview');

            const durationElement =
                document.getElementById('planDuration');

            const profileViewsElement =
                document.getElementById('planProfileViews');

            const contactViewsElement =
                document.getElementById('planContactViews');

            const costElement =
                document.getElementById('planCost');

            const expiryElement =
                document.getElementById('membershipExpiryDate');


            function updatePlanPreview() {

                const option =
                    planSelect.options[planSelect.selectedIndex];


                if (!option || !option.value) {

                    details.classList.add('d-none');

                    expiryPreview.classList.add('d-none');

                    return;
                }


                const duration =
                    parseInt(
                        option.dataset.duration || 0,
                        10
                    );


                const profileViews =
                    option.dataset.profileViews || '0';


                const contactViews =
                    option.dataset.contactViews || '0';


                const cost =
                    parseFloat(
                        option.dataset.cost || 0
                    );


                durationElement.textContent =
                    duration + (duration === 1 ? ' Day' : ' Days');


                profileViewsElement.textContent =
                    profileViews;


                contactViewsElement.textContent =
                    contactViews;


                costElement.textContent =
                    '₹' + cost.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });


                details.classList.remove('d-none');

                updateExpiryDate();

            }


            function updateExpiryDate() {

                const option =
                    planSelect.options[planSelect.selectedIndex];


                if (!option || !option.value) {
                    return;
                }


                const duration =
                    parseInt(
                        option.dataset.duration || 0,
                        10
                    );


                const dateValue =
                    activationDate.value;


                if (!dateValue || !duration) {

                    expiryPreview.classList.add('d-none');

                    return;
                }


                const date =
                    new Date(dateValue + 'T00:00:00');


                /*
                |--------------------------------------------------------------------------
                | Activation date is Day 1
                |--------------------------------------------------------------------------
                */

                date.setDate(
                    date.getDate() + duration - 1
                );


                const day =
                    String(date.getDate()).padStart(2, '0');


                const month =
                    String(date.getMonth() + 1).padStart(2, '0');


                const year =
                    date.getFullYear();


                expiryElement.textContent =
                    `${day}-${month}-${year}`;


                expiryPreview.classList.remove('d-none');

            }


            planSelect.addEventListener(
                'change',
                updatePlanPreview
            );


            activationDate.addEventListener(
                'change',
                updateExpiryDate
            );


            /*
            |--------------------------------------------------------------------------
            | Initialize if a plan is already selected
            |--------------------------------------------------------------------------
            */

            updatePlanPreview();

        });
    </script>

    @endpush
