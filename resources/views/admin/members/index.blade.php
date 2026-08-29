@extends('admin.layout')

@section('title', 'Members')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">Members</h1>

            <p class="text-muted mb-0">
                Manage members of the selected site.
            </p>
        </div>
        <a
            href="{{ route('admin.members.create') }}"
            class="btn btn-primary">

            <i class="bi bi-plus-lg me-1"></i>

            Add Member

        </a>
    </div>


    {{-- Search / Filter --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('admin.members.index') }}">

                <div class="row g-3">

                    {{-- Search --}}
                    <div class="col-lg-4 col-md-6">

                        <label class="form-label fw-semibold">
                            Search Member
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control"
                                placeholder="Profile ID, name, email or mobile">

                        </div>

                    </div>


                    {{-- Status --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option
                                value="active"
                                {{ request('status') === 'active' ? 'selected' : '' }}>

                                Active

                            </option>

                            <option
                                value="inactive"
                                {{ request('status') === 'inactive' ? 'selected' : '' }}>

                                Inactive

                            </option>

                        </select>

                    </div>


                    {{-- Trusted --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="form-label fw-semibold">
                            Trusted
                        </label>

                        <select
                            name="trusted"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option
                                value="yes"
                                {{ request('trusted') === 'yes' ? 'selected' : '' }}>

                                Trusted

                            </option>

                            <option
                                value="no"
                                {{ request('trusted') === 'no' ? 'selected' : '' }}>

                                Not Trusted

                            </option>

                        </select>

                    </div>

                    {{-- Banned Status --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="form-label fw-semibold">
                            Banned
                        </label>

                        <select
                            name="banned"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option
                                value="yes"
                                {{ request('banned') === 'yes' ? 'selected' : '' }}>
                                Banned
                            </option>

                            <option
                                value="no"
                                {{ request('banned') === 'no' ? 'selected' : '' }}>
                                Not Banned
                            </option>

                        </select>

                    </div>


                    {{-- Promoted --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="form-label fw-semibold">
                            Promoted
                        </label>

                        <select
                            name="promoted"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option
                                value="yes"
                                {{ request('promoted') === 'yes' ? 'selected' : '' }}>

                                Promoted

                            </option>

                            <option
                                value="no"
                                {{ request('promoted') === 'no' ? 'selected' : '' }}>

                                Not Promoted

                            </option>

                        </select>

                    </div>


                    {{-- Visibility --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="form-label fw-semibold">
                            Visibility
                        </label>

                        <select
                            name="visibility"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option
                                value="visible"
                                {{ request('visibility') === 'visible' ? 'selected' : '' }}>

                                Visible

                            </option>

                            <option
                                value="hidden"
                                {{ request('visibility') === 'hidden' ? 'selected' : '' }}>

                                Hidden

                            </option>

                        </select>

                    </div>

                    {{-- Membership Plan --}}

                    <div class="col-lg-2 col-md-6">

                        <label class="form-label fw-semibold">
                            Membership Plan
                        </label>

                        <select
                            name="plan_id"
                            class="form-select">

                            <option value="">
                                All Plans
                            </option>

                            <option
                                value="none"
                                {{ request('plan_id') === 'none' ? 'selected' : '' }}>

                                No Plan

                            </option>

                            @foreach($plans as $plan)

                            <option
                                value="{{ $plan->id }}"
                                {{ request('plan_id') == $plan->id ? 'selected' : '' }}>

                                {{ $plan->plan_name }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- Sort --}}

                    <div class="col-lg-2 col-md-6">

                        <label class="form-label fw-semibold">
                            Sort By
                        </label>

                        <select
                            name="sort"
                            class="form-select">

                            <option
                                value="newest"
                                {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>
                                Newest First
                            </option>

                            <option
                                value="oldest"
                                {{ request('sort') === 'oldest' ? 'selected' : '' }}>
                                Oldest First
                            </option>

                            <option
                                value="name_asc"
                                {{ request('sort') === 'name_asc' ? 'selected' : '' }}>
                                Name A-Z
                            </option>

                            <option
                                value="name_desc"
                                {{ request('sort') === 'name_desc' ? 'selected' : '' }}>
                                Name Z-A
                            </option>

                            <option
                                value="profile_asc"
                                {{ request('sort') === 'profile_asc' ? 'selected' : '' }}>
                                Profile ID A-Z
                            </option>

                            <option
                                value="profile_desc"
                                {{ request('sort') === 'profile_desc' ? 'selected' : '' }}>
                                Profile ID Z-A
                            </option>

                        </select>

                    </div>


                    {{-- Buttons --}}
                    <div class="col-12">

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                <i class="bi bi-funnel me-1"></i>
                                Apply Filters

                            </button>


                            <a
                                href="{{ route('admin.members.index') }}"
                                class="btn btn-outline-secondary">

                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                Reset

                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

    {{-- =========================================================
    Active Filters
========================================================= --}}

    @php
    $hasFilters =
    request()->filled('search') ||
    request()->filled('status') ||
    request()->filled('trusted') ||
    request()->filled('promoted') ||
    request()->filled('visibility') ||
    request()->filled('banned') ||
    request()->filled('plan_id') ||
    request()->filled('sort');
    @endphp


    @if($hasFilters)

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body py-3">

            <div class="d-flex flex-wrap align-items-center gap-2">

                <span class="fw-semibold text-muted me-1">
                    <i class="bi bi-funnel me-1"></i>
                    Active Filters:
                </span>


                {{-- Search --}}

                @if(request()->filled('search'))

                <span class="badge bg-primary d-flex align-items-center gap-1">

                    Search:
                    {{ request('search') }}

                    <a
                        href="{{ request()->fullUrlWithQuery([
                                'search' => null,
                                'page' => null
                            ]) }}"
                        class="text-white text-decoration-none ms-1"
                        title="Remove search">

                        &times;

                    </a>

                </span>

                @endif


                {{-- Status --}}

                @if(request()->filled('status'))

                <span class="badge bg-secondary d-flex align-items-center gap-1">

                    Status:
                    {{ request('status') === 'active' ? 'Active' : 'Inactive' }}

                    <a
                        href="{{ request()->fullUrlWithQuery([
                                'status' => null,
                                'page' => null
                            ]) }}"
                        class="text-white text-decoration-none ms-1"
                        title="Remove status">

                        &times;

                    </a>

                </span>

                @endif


                {{-- Trusted --}}

                @if(request()->filled('trusted'))

                <span class="badge bg-info text-dark d-flex align-items-center gap-1">

                    Trusted:
                    {{ request('trusted') === 'yes' ? 'Yes' : 'No' }}

                    <a
                        href="{{ request()->fullUrlWithQuery([
                                'trusted' => null,
                                'page' => null
                            ]) }}"
                        class="text-dark text-decoration-none ms-1"
                        title="Remove trusted filter">

                        &times;

                    </a>

                </span>

                @endif

                {{-- Banned --}}

                @if(request()->filled('banned'))

                <span class="badge bg-danger d-flex align-items-center gap-1">

                    Banned:
                    {{ request('banned') === 'yes' ? 'Yes' : 'No' }}

                    <a
                        href="{{ request()->fullUrlWithQuery([
                'banned' => null,
                'page' => null
            ]) }}"
                        class="text-white text-decoration-none ms-1"
                        title="Remove banned filter">

                        &times;

                    </a>

                </span>

                @endif


                {{-- Promoted --}}

                @if(request()->filled('promoted'))

                <span class="badge bg-warning text-dark d-flex align-items-center gap-1">

                    Promoted:
                    {{ request('promoted') === 'yes' ? 'Yes' : 'No' }}

                    <a
                        href="{{ request()->fullUrlWithQuery([
                                'promoted' => null,
                                'page' => null
                            ]) }}"
                        class="text-dark text-decoration-none ms-1"
                        title="Remove promoted filter">

                        &times;

                    </a>

                </span>

                @endif


                {{-- Visibility --}}

                @if(request()->filled('visibility'))

                <span class="badge bg-dark d-flex align-items-center gap-1">

                    Visibility:
                    {{ request('visibility') === 'visible' ? 'Visible' : 'Hidden' }}

                    <a
                        href="{{ request()->fullUrlWithQuery([
                                'visibility' => null,
                                'page' => null
                            ]) }}"
                        class="text-white text-decoration-none ms-1"
                        title="Remove visibility filter">

                        &times;

                    </a>

                </span>

                @endif


                {{-- Membership Plan --}}

                @if(request()->filled('plan_id'))

                @php
                $selectedPlan = null;

                if (request('plan_id') !== 'none') {
                $selectedPlan = $plans->firstWhere(
                'id',
                request('plan_id')
                );
                }
                @endphp


                <span class="badge bg-primary d-flex align-items-center gap-1">

                    Plan:

                    @if(request('plan_id') === 'none')

                    No Plan

                    @elseif($selectedPlan)

                    {{ $selectedPlan->plan_name }}

                    @else

                    Unknown Plan

                    @endif


                    <a
                        href="{{ request()->fullUrlWithQuery([
                                'plan_id' => null,
                                'page' => null
                            ]) }}"
                        class="text-white text-decoration-none ms-1"
                        title="Remove membership filter">

                        &times;

                    </a>

                </span>

                @endif


                {{-- Sort --}}

                @if(request()->filled('sort'))

                @php
                $sortLabels = [
                'newest' => 'Newest First',
                'oldest' => 'Oldest First',
                'name_asc' => 'Name A-Z',
                'name_desc' => 'Name Z-A',
                'profile_asc' => 'Profile ID A-Z',
                'profile_desc' => 'Profile ID Z-A',
                ];

                $sortLabel = $sortLabels[request('sort')] ?? request('sort');
                @endphp


                <span class="badge bg-light text-dark border d-flex align-items-center gap-1">

                    Sort:
                    {{ $sortLabel }}

                    <a
                        href="{{ request()->fullUrlWithQuery([
                                'sort' => null,
                                'page' => null
                            ]) }}"
                        class="text-dark text-decoration-none ms-1"
                        title="Remove sorting">

                        &times;

                    </a>

                </span>

                @endif


                {{-- Clear All --}}

                <a
                    href="{{ route('admin.members.index') }}"
                    class="btn btn-sm btn-outline-danger ms-1">

                    <i class="bi bi-x-circle me-1"></i>
                    Clear All

                </a>

            </div>

        </div>

    </div>

    @endif


    {{-- Members Table --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>Profile ID</th>

                            <th>Member</th>

                            <th>Mobile</th>

                            <th>Gender</th>

                            <th>Registration</th>

                            <th>Membership</th>

                            <th>Status</th>

                            <th class="text-end">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($members as $member)

                        <tr>

                            {{-- Profile ID --}}
                            <td>

                                <strong>
                                    {{ $member->profile_id }}
                                </strong>

                            </td>


                            {{-- Member --}}
                            <td>

                                <div>

                                    <strong>
                                        {{ $member->full_name }}
                                    </strong>

                                    <div class="small text-muted">
                                        {{ $member->email }}
                                    </div>

                                </div>

                            </td>


                            {{-- Mobile --}}
                            <td>
                                {{ $member->mobile_number }}
                            </td>


                            {{-- Gender --}}
                            <td>
                                {{ $member->gender }}
                            </td>


                            {{-- Registration --}}
                            <td>
                                {{ $member->registration_date }}
                            </td>

                            <td>
                                @if(!empty($member->membership_plan_name))

                                @php
                                $planName = strtolower($member->membership_plan_name);

                                $planClass = match (true) {
                                str_contains($planName, 'platinum'),
                                str_contains($planName, 'vip') => 'bg-danger',

                                str_contains($planName, 'premium') => 'bg-primary',

                                str_contains($planName, 'gold') => 'bg-warning text-dark',

                                str_contains($planName, 'silver') => 'bg-info text-dark',

                                str_contains($planName, 'basic'),
                                str_contains($planName, 'free') => 'bg-secondary',

                                default => 'bg-dark',
                                };
                                @endphp

                                <span class="badge {{ $planClass }}">
                                    {{ $member->membership_plan_name }}
                                </span>

                                @else

                                <span class="badge bg-light text-muted border">
                                    No Plan
                                </span>

                                @endif
                            </td>

                            {{-- Status --}}
                            <td>

                                @if($member->active === 'Yes')

                                <span class="badge bg-success">
                                    Active
                                </span>

                                @else

                                <span class="badge bg-secondary">
                                    Inactive
                                </span>

                                @endif

                            </td>


                            <td class="text-end">

                                <div class="dropdown">

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">

                                        <i class="bi bi-three-dots-vertical"></i>
                                        Actions

                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">

                                        {{-- View Profile --}}
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="{{ route('admin.members.show', $member->id) }}">

                                                <i class="bi bi-person me-2 text-primary"></i>
                                                View Profile

                                            </a>
                                        </li>


                                        {{-- Edit Profile --}}
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="{{ route('admin.members.edit', $member->id) }}">

                                                <i class="bi bi-pencil me-2 text-primary"></i>
                                                Edit Profile

                                            </a>
                                        </li>

                                        @if(auth('admin')->user()?->hasPermission('raise-profile-delete-request'))

                                        <button
                                            type="button"
                                            class="dropdown-item text-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteRequestModal"
                                            data-action="{{ route('admin.members.delete-request', $member->id) }}"
                                            data-member-id="{{ $member->id }}"
                                            data-profile-id="{{ $member->profile_id }}"
                                            data-member-name="{{ $member->full_name }}">
                                            <i class="bi bi-trash3 me-2"></i>
                                            Raise Delete Request
                                        </button>

                                        @endif


                                        {{-- Activity --}}
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="{{ route('admin.activities.member', [
                        'memberId' => $member->id,
                        'activity' => 'shortlisted'
                    ]) }}">

                                                <i class="bi bi-activity me-2 text-info"></i>
                                                View Activity

                                            </a>
                                        </li>


                                        {{-- Photos --}}
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="{{ route('admin.members.show', $member->id) }}#gallery">

                                                <i class="bi bi-images me-2 text-info"></i>
                                                Manage Photos

                                            </a>
                                        </li>


                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>


                                        {{-- Trusted --}}
                                        <li>

                                            <form
                                                action="{{ route('admin.members.toggle-trusted', $member->id) }}"
                                                method="POST"
                                                class="member-action-form"
                                                data-confirm-title="Change Trusted Status"
                                                data-confirm="Are you sure you want to change this member's trusted status?">

                                                @csrf

                                                <button type="submit" class="dropdown-item">
                                                    @if($member->is_trusted === 'Yes')
                                                    <i class="bi bi-patch-check-fill me-2 text-success"></i>
                                                    Remove Trusted
                                                    @else
                                                    <i class="bi bi-patch-check me-2 text-success"></i>
                                                    Mark as Trusted
                                                    @endif
                                                </button>

                                            </form>

                                        </li>


                                        {{-- Promoted --}}
                                        <li>

                                            <form
                                                action="{{ route('admin.members.toggle-promoted', $member->id) }}"
                                                method="POST"
                                                class="member-action-form"
                                                data-confirm-title="Change Promotion Status"
                                                data-confirm="Are you sure you want to change this member's promotion status?">

                                                @csrf

                                                <button type="submit" class="dropdown-item">
                                                    @if($member->promoted === 'Yes')
                                                    <i class="bi bi-star-fill me-2 text-warning"></i>
                                                    Remove Promotion
                                                    @else
                                                    <i class="bi bi-star me-2 text-warning"></i>
                                                    Promote Member
                                                    @endif
                                                </button>

                                            </form>

                                        </li>


                                        {{-- Visibility --}}
                                        <li>

                                            <form
                                                action="{{ route('admin.members.toggle-visibility', $member->id) }}"
                                                method="POST"
                                                class="member-action-form"
                                                data-confirm-title="Change Profile Visibility"
                                                data-confirm="Are you sure you want to change this member's profile visibility?">

                                                @csrf

                                                <button type="submit" class="dropdown-item">
                                                    @if($member->profile_hide === 'Yes')
                                                    <i class="bi bi-eye me-2 text-success"></i>
                                                    Show Profile
                                                    @else
                                                    <i class="bi bi-eye-slash me-2 text-warning"></i>
                                                    Hide Profile
                                                    @endif
                                                </button>

                                            </form>

                                        </li>


                                        {{-- Active / Inactive --}}
                                        <li>

                                            <form
                                                action="{{ route('admin.members.toggle-status', $member->id) }}"
                                                method="POST"
                                                class="member-action-form"
                                                data-confirm-title="Change Member Status"
                                                data-confirm="Are you sure you want to change this member's active status?">

                                                @csrf

                                                <button type="submit" class="dropdown-item">

                                                    @if($member->active === 'Yes')
                                                    <i class="bi bi-person-x me-2 text-danger"></i>
                                                    Deactivate Member
                                                    @else
                                                    <i class="bi bi-person-check me-2 text-success"></i>
                                                    Activate Member
                                                    @endif

                                                </button>

                                            </form>

                                        </li>

                                        <li>
                                            <button
                                                type="button"
                                                class="dropdown-item"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rotationModal"
                                                data-member-id="{{ $member->id }}"
                                                data-member-name="{{ $member->full_name }}">

                                                <i class="bi bi-arrow-repeat me-2"></i>
                                                Add Rotation

                                            </button>
                                        </li>

                                    </ul>

                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5 text-muted">
                                No members found.
                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}
        @if($members->hasPages())

        <div class="card-footer bg-white">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

                <div class="text-muted small">

                    Showing
                    <strong>{{ $members->firstItem() }}</strong>
                    to
                    <strong>{{ $members->lastItem() }}</strong>
                    of
                    <strong>{{ $members->total() }}</strong>
                    members

                </div>

                <div>
                    {{ $members->onEachSide(1)->links() }}
                </div>

            </div>

        </div>

        @endif

    </div>

</div>

{{-- =========================================================
    Member Action Confirmation Modal
========================================================= --}}

<div
    class="modal fade"
    id="memberActionModal"
    tabindex="-1"
    aria-labelledby="memberActionModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="memberActionModalLabel">

                    Confirm Action

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>


            <div class="modal-body">

                <div class="d-flex align-items-start">

                    <div
                        class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                        style="width:45px;height:45px;">

                        <i class="bi bi-exclamation-triangle fs-5"></i>

                    </div>

                    <div>

                        <p
                            class="mb-0"
                            id="memberActionModalMessage">

                            Are you sure you want to perform this action?

                        </p>

                    </div>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">

                    Cancel

                </button>

                <button
                    type="button"
                    class="btn btn-primary"
                    id="memberActionConfirmBtn">

                    Confirm

                </button>

            </div>

        </div>

    </div>

</div>

{{-- =========================================================
     MEMBER ROTATION MODAL
========================================================= --}}

<div
    class="modal fade"
    id="rotationModal"
    tabindex="-1"
    aria-labelledby="rotationModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="rotationModalLabel">
                    Set Member Rotation
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>

            <form
                method="POST"
                id="rotationForm">

                @csrf

                <div class="modal-body">

                    {{-- Member --}}
                    <div class="mb-3">

                        <label
                            for="rotationMemberName"
                            class="form-label">
                            Member
                        </label>

                        <input
                            type="text"
                            id="rotationMemberName"
                            class="form-control"
                            readonly>

                    </div>


                    {{-- Days --}}
                    <div class="mb-3">

                        <label
                            for="rotationDays"
                            class="form-label">
                            Rotation Days
                        </label>

                        <input
                            type="number"
                            name="days"
                            id="rotationDays"
                            class="form-control"
                            min="1"
                            max="365"
                            value="7"
                            required>

                        <small class="text-muted">
                            Number of days until the next rotation.
                        </small>

                    </div>


                    {{-- Time --}}
                    <div class="mb-3">

                        <label
                            for="rotationTime"
                            class="form-label">
                            Rotation Time
                        </label>

                        <input
                            type="time"
                            name="time"
                            id="rotationTime"
                            class="form-control"
                            required>

                    </div>


                    {{-- Preview --}}
                    <div
                        class="alert alert-light border"
                        id="rotationPreview">

                        Next rotation will be calculated after saving.

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-check-circle me-1"></i>

                        Save Rotation

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        let selectedForm = null;

        const modalElement = document.getElementById('memberActionModal');

        const modal = new bootstrap.Modal(modalElement);

        const titleElement = document.getElementById(
            'memberActionModalLabel'
        );

        const messageElement = document.getElementById(
            'memberActionModalMessage'
        );

        const confirmButton = document.getElementById(
            'memberActionConfirmBtn'
        );


        /*
        |--------------------------------------------------------------------------
        | Open Confirmation Modal
        |--------------------------------------------------------------------------
        */

        document.querySelectorAll('.member-action-form').forEach(function(form) {

            form.addEventListener('submit', function(event) {

                event.preventDefault();

                selectedForm = form;

                const title =
                    form.dataset.confirmTitle ||
                    'Confirm Action';

                const message =
                    form.dataset.confirmMessage ||
                    'Are you sure you want to perform this action?';


                titleElement.textContent = title;

                messageElement.textContent = message;


                modal.show();

            });

        });


        /*
        |--------------------------------------------------------------------------
        | Confirm Action
        |--------------------------------------------------------------------------
        */

        confirmButton.addEventListener('click', function() {

            if (!selectedForm) {
                return;
            }


            /*
            | Prevent double-clicks
            */

            confirmButton.disabled = true;

            confirmButton.innerHTML = `
            <span
                class="spinner-border spinner-border-sm me-1"
                role="status"
                aria-hidden="true">
            </span>

            Processing...
        `;


            /*
            | Submit original form
            */

            selectedForm.submit();

        });


        /*
        |--------------------------------------------------------------------------
        | Reset Modal
        |--------------------------------------------------------------------------
        */

        modalElement.addEventListener('hidden.bs.modal', function() {

            selectedForm = null;

            confirmButton.disabled = false;

            confirmButton.innerHTML = 'Confirm';

        });

    });

    document.addEventListener('DOMContentLoaded', function() {

        const rotationModal = document.getElementById('rotationModal');

        const rotationForm = document.getElementById('rotationForm');

        const rotationMemberName =
            document.getElementById('rotationMemberName');

        const rotationDays =
            document.getElementById('rotationDays');

        const rotationTime =
            document.getElementById('rotationTime');

        const rotationPreview =
            document.getElementById('rotationPreview');


        /*
        |--------------------------------------------------------------------------
        | Check Elements
        |--------------------------------------------------------------------------
        */

        if (!rotationModal) {
            console.error('Rotation modal not found.');
            return;
        }

        if (!rotationForm) {
            console.error('Rotation form not found.');
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Get Current Time
        |--------------------------------------------------------------------------
        */

        function getCurrentTime() {

            const now = new Date();

            const hours =
                String(now.getHours()).padStart(2, '0');

            const minutes =
                String(now.getMinutes()).padStart(2, '0');

            return `${hours}:${minutes}`;
        }


        /*
        |--------------------------------------------------------------------------
        | Update Rotation Preview
        |--------------------------------------------------------------------------
        */

        function updateRotationPreview() {

            const days =
                parseInt(rotationDays.value, 10);

            const time =
                rotationTime.value;


            if (!days || days < 1 || !time) {

                rotationPreview.innerHTML =
                    'Enter rotation days and time.';

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Calculate Next Rotation Date
            |--------------------------------------------------------------------------
            */

            const nextDate = new Date();

            nextDate.setHours(0, 0, 0, 0);

            nextDate.setDate(
                nextDate.getDate() + days
            );


            /*
            |--------------------------------------------------------------------------
            | Format Date
            |--------------------------------------------------------------------------
            */

            const dateString =
                nextDate.toLocaleDateString(
                    'en-IN', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    }
                );


            /*
            |--------------------------------------------------------------------------
            | Format Time
            |--------------------------------------------------------------------------
            */

            const [hours, minutes] =
            time.split(':');


            const displayTime =
                new Date();

            displayTime.setHours(
                parseInt(hours, 10),
                parseInt(minutes, 10),
                0,
                0
            );


            const timeString =
                displayTime.toLocaleTimeString(
                    'en-IN', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }
                );


            /*
            |--------------------------------------------------------------------------
            | Show Preview
            |--------------------------------------------------------------------------
            */

            rotationPreview.innerHTML = `
            <strong>Next Rotation:</strong>
            ${dateString} at ${timeString}
        `;
        }


        /*
        |--------------------------------------------------------------------------
        | Bootstrap Modal Open
        |--------------------------------------------------------------------------
        */

        rotationModal.addEventListener(
            'show.bs.modal',
            function(event) {

                const button =
                    event.relatedTarget;


                if (!button) {

                    console.error(
                        'Rotation modal opened without a trigger button.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Get Member Information
                |--------------------------------------------------------------------------
                */

                const memberId =
                    button.getAttribute('data-member-id');

                const memberName =
                    button.getAttribute('data-member-name');


                console.log(
                    'Opening rotation for member:',
                    memberId,
                    memberName
                );


                /*
                |--------------------------------------------------------------------------
                | Member Name
                |--------------------------------------------------------------------------
                */

                rotationMemberName.value =
                    memberName || '';


                /*
                |--------------------------------------------------------------------------
                | Form Action
                |--------------------------------------------------------------------------
                */

                if (memberId) {

                    rotationForm.action =
                        `/admin/members/${memberId}/rotation`;

                } else {

                    console.error(
                        'Member ID missing from rotation button.'
                    );

                    rotationForm.action = '';
                }


                /*
                |--------------------------------------------------------------------------
                | Default Days
                |--------------------------------------------------------------------------
                */

                rotationDays.value = 7;


                /*
                |--------------------------------------------------------------------------
                | Default Time
                |--------------------------------------------------------------------------
                */

                rotationTime.value =
                    getCurrentTime();


                /*
                |--------------------------------------------------------------------------
                | Update Preview
                |--------------------------------------------------------------------------
                */

                updateRotationPreview();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Days Changed
        |--------------------------------------------------------------------------
        */

        rotationDays.addEventListener(
            'input',
            updateRotationPreview
        );


        /*
        |--------------------------------------------------------------------------
        | Time Changed
        |--------------------------------------------------------------------------
        */

        rotationTime.addEventListener(
            'change',
            updateRotationPreview
        );


        /*
        |--------------------------------------------------------------------------
        | Also Update When User Types Time
        |--------------------------------------------------------------------------
        */

        rotationTime.addEventListener(
            'input',
            updateRotationPreview
        );

    });
</script>

@endpush
