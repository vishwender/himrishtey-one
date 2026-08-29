@extends('admin.layout')

@section('title', 'Edit Staff User')

@section('content')

<div class="mb-4">

    <a
        href="{{ route('admin.staff-users.index') }}"
        class="text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>
        Back to Staff Users
    </a>

    <div class="mt-3">
        <h1 class="h3 mb-1">Edit Staff User</h1>

        <p class="text-muted mb-0">
            Update staff account information, role and site access.
        </p>
    </div>

</div>

@if($errors->any())

<div class="alert alert-danger">

    <div class="fw-semibold mb-2">
        Please fix the following errors:
    </div>

    <ul class="mb-0">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>

</div>

@endif

<form
    method="POST"
    action="{{ route('admin.staff-users.update', $admin) }}">

    @csrf
    @method('PUT')

    <div class="row g-4">

        {{-- Account --}}
        <div class="col-lg-8">

            <div class="card">

                <div class="card-header bg-transparent">
                    <h5 class="mb-1">Account Information</h5>

                    <small class="text-muted">
                        Update the staff account information.
                    </small>
                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $admin->name) }}"
                                required>

                            @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Profile ID
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $admin->profile_id }}"
                                readonly>

                            <div class="form-text">
                                Profile IDs are generated automatically.
                            </div>

                        </div>

                        <div class="col-12">

                            <label class="form-label">
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $admin->email) }}"
                                required>

                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                New Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Leave blank to keep current password">

                            @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Confirm New Password
                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Repeat new password">

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Access --}}
        <div class="col-lg-4">

            <div class="card mb-4">

                <div class="card-header bg-transparent">
                    <h5 class="mb-1">Role</h5>

                    <small class="text-muted">
                        Select the staff member's role.
                    </small>
                </div>

                <div class="card-body">

                    @php
                    $currentRoleId = old(
                    'role_id',
                    $admin->roles->first()?->id
                    );
                    @endphp

                    @forelse($roles as $role)

                    <div class="form-check staff-role-option">

                        <input
                            class="form-check-input"
                            type="radio"
                            name="role_id"
                            value="{{ $role->id }}"
                            id="role_{{ $role->id }}"
                            {{ $currentRoleId == $role->id ? 'checked' : '' }}>

                        <label
                            class="form-check-label"
                            for="role_{{ $role->id }}">

                            <strong class="d-block">
                                {{ $role->name }}
                            </strong>

                            @if($role->description)
                            <small class="text-muted">
                                {{ $role->description }}
                            </small>
                            @endif

                        </label>

                    </div>

                    @empty

                    <div class="text-muted">
                        No roles available.
                    </div>

                    @endforelse

                    @error('role_id')
                    <div class="text-danger small mt-2">
                        {{ $message }}
                    </div>
                    @enderror

                </div>

            </div>

            <div class="card staff-access-card">

                <div class="card-header bg-transparent">

                    <h5 class="mb-1">
                        Site Access
                    </h5>

                    <small class="text-muted">
                        Select the sites this staff member can access.
                    </small>

                </div>

                <div class="card-body">

                    @php
                    $currentSiteIds = old(
                    'sites',
                    $admin->sites->pluck('id')->toArray()
                    );
                    @endphp

                    @forelse($sites as $site)

                    <div class="form-check staff-site-option">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="sites[]"
                            value="{{ $site->id }}"
                            id="site_{{ $site->id }}"
                            {{ in_array($site->id, $currentSiteIds) ? 'checked' : '' }}>

                        <label
                            class="form-check-label"
                            for="site_{{ $site->id }}">
                            {{ $site->name }}
                        </label>

                    </div>

                    @empty

                    <div class="text-muted">
                        No active sites available.
                    </div>

                    @endforelse

                </div>

            </div>

        </div>

        {{-- Submit --}}
        <div class="col-12">

            <div class="d-flex justify-content-between align-items-center">

                <div class="small text-muted">
                    Account status:
                    @if($admin->status)
                    <span class="text-success fw-semibold">Active</span>
                    @else
                    <span class="text-danger fw-semibold">Inactive</span>
                    @endif
                </div>

                <div class="d-flex gap-2">

                    <a
                        href="{{ route('admin.staff-users.index') }}"
                        class="btn btn-light border">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Save Changes
                    </button>

                </div>

            </div>

        </div>

    </div>

</form>

@endsection
