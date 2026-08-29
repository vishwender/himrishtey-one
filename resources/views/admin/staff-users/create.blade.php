@extends('admin.layout')

@section('title', 'Add Staff User')

@section('content')

<div class="content">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="mb-1">Add Staff User</h1>
            <p class="text-muted mb-0">
                Create a new HimRishtey staff account and assign access.
            </p>
        </div>

        <a href="{{ route('admin.staff-users.index') }}"
            class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Back to Staff Users

        </a>

    </div>


    {{-- Validation Errors --}}
    @if($errors->any())

    <div class="alert alert-danger mb-4">

        <div class="fw-semibold mb-2">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Please fix the following errors:
        </div>

        <ul class="mb-0 ps-4">

            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach

        </ul>

    </div>

    @endif


    <form action="{{ route('admin.staff-users.store') }}"
        method="POST">

        @csrf

        <div class="row g-4">

            {{-- =====================================================
                 BASIC INFORMATION
            ====================================================== --}}
            <div class="col-lg-8">

                <div class="card">

                    <div class="card-header bg-transparent">

                        <h5 class="mb-1">
                            Staff Information
                        </h5>

                        <p class="text-muted small mb-0">
                            Enter the basic information for this staff account.
                        </p>

                    </div>


                    <div class="card-body">

                        <div class="row g-4">

                            {{-- Name --}}
                            <div class="col-md-6">

                                <label for="name"
                                    class="form-label">

                                    Full Name
                                    <span class="text-danger">*</span>

                                </label>

                                <input type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Enter staff name"
                                    required>

                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>


                            {{-- Email --}}
                            <div class="col-md-6">

                                <label for="email"
                                    class="form-label">

                                    Email Address
                                    <span class="text-danger">*</span>

                                </label>

                                <input type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="staff@himrishtey.com"
                                    required>

                                @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>


                            {{-- Password --}}
                            <div class="col-md-6">

                                <label for="password"
                                    class="form-label">

                                    Password
                                    <span class="text-danger">*</span>

                                </label>

                                <div class="input-group">

                                    <input type="password"
                                        id="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Minimum 8 characters"
                                        required>

                                    <button type="button"
                                        class="btn btn-outline-secondary password-toggle"
                                        data-target="password">

                                        <i class="bi bi-eye"></i>

                                    </button>

                                </div>

                                @error('password')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>


                            {{-- Confirm Password --}}
                            <div class="col-md-6">

                                <label for="password_confirmation"
                                    class="form-label">

                                    Confirm Password
                                    <span class="text-danger">*</span>

                                </label>

                                <div class="input-group">

                                    <input type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        class="form-control"
                                        placeholder="Confirm password"
                                        required>

                                    <button type="button"
                                        class="btn btn-outline-secondary password-toggle"
                                        data-target="password_confirmation">

                                        <i class="bi bi-eye"></i>

                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 ROLE
            ====================================================== --}}
            <div class="col-lg-4">

                <div class="card">

                    <div class="card-header bg-transparent">

                        <h5 class="mb-1">
                            Staff Role
                        </h5>

                        <p class="text-muted small mb-0">
                            Select the role for this staff member.
                        </p>

                    </div>


                    <div class="card-body">

                        <label for="role_id"
                            class="form-label">

                            Role
                            <span class="text-danger">*</span>

                        </label>

                        <select name="role_id"
                            id="role_id"
                            class="form-select @error('role_id') is-invalid @enderror"
                            required>

                            <option value="">
                                Select Role
                            </option>

                            @foreach($roles as $role)

                            <option value="{{ $role->id }}"
                                {{ old('role_id') == $role->id ? 'selected' : '' }}>

                                {{ $role->name }}

                            </option>

                            @endforeach

                        </select>

                        @error('role_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                        <div class="form-text mt-2">
                            The role determines what this staff member
                            can do inside the admin panel.
                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 SITE ACCESS
            ====================================================== --}}
            <div class="col-12">

                <div class="card">

                    <div class="card-header bg-transparent">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <h5 class="mb-1">
                                    Site Access
                                </h5>

                                <p class="text-muted small mb-0">
                                    Select which HimRishtey sites this staff member
                                    can access.
                                </p>

                            </div>

                            <button type="button"
                                class="btn btn-sm btn-outline-primary"
                                id="selectAllSites">

                                Select All

                            </button>

                        </div>

                    </div>


                    <div class="card-body">

                        @if($sites->count())

                        <div class="row g-3">

                            @foreach($sites as $site)

                            <div class="col-md-6 col-lg-4">

                                <label class="site-option">

                                    <input type="checkbox"
                                        name="sites[]"
                                        value="{{ $site->id }}"
                                        class="site-checkbox"
                                        {{ in_array($site->id, old('sites', [])) ? 'checked' : '' }}>

                                    <span class="site-option-content">

                                        <span class="site-icon">
                                            <i class="bi bi-globe2"></i>
                                        </span>

                                        <span>

                                            <strong>
                                                {{ $site->name }}
                                            </strong>

                                            @if(!empty($site->domain))
                                            <small>
                                                {{ $site->domain }}
                                            </small>
                                            @endif

                                        </span>

                                    </span>

                                </label>

                            </div>

                            @endforeach

                        </div>

                        @else

                        <div class="text-center py-4">

                            <i class="bi bi-globe2 fs-2 text-muted"></i>

                            <p class="text-muted mt-2 mb-0">
                                No active sites are available.
                            </p>

                        </div>

                        @endif

                        @error('sites')
                        <div class="text-danger small mt-3">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 ACTIONS
            ====================================================== --}}
            <div class="col-12">

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('admin.staff-users.index') }}"
                        class="btn btn-outline-secondary">

                        Cancel

                    </a>

                    <button type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-person-plus me-1"></i>
                        Create Staff User

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>


<style>
    /*
    |--------------------------------------------------------------------------
    | Site Selection
    |--------------------------------------------------------------------------
    */

    .site-option {
        display: block;
        cursor: pointer;
        margin: 0;
    }

    .site-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .site-option-content {
        display: flex;
        align-items: center;
        gap: 12px;

        min-height: 72px;
        padding: 14px;

        border: 1px solid var(--app-border);
        border-radius: 12px;

        background: var(--app-surface);

        transition:
            border-color .2s ease,
            background .2s ease,
            box-shadow .2s ease,
            transform .2s ease;
    }

    .site-option-content:hover {
        border-color: var(--app-primary);
        transform: translateY(-1px);
    }

    .site-option input:checked+.site-option-content {
        border-color: var(--app-primary);
        background: rgba(109, 74, 255, .07);
        box-shadow: 0 0 0 2px rgba(109, 74, 255, .08);
    }

    .site-icon {
        width: 40px;
        height: 40px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 10px;

        background: #eeeaff;
        color: var(--app-primary);

        font-size: 1rem;
    }

    .site-option-content strong {
        display: block;
        color: var(--app-ink);
        font-size: .9rem;
    }

    .site-option-content small {
        display: block;
        margin-top: 2px;
        color: var(--app-muted);
        font-size: .78rem;
    }


    /*
    |--------------------------------------------------------------------------
    | Dark Mode
    |--------------------------------------------------------------------------
    */

    [data-theme="dark"] .site-option-content {
        background: var(--app-surface);
        border-color: var(--app-border);
    }

    [data-theme="dark"] .site-option input:checked+.site-option-content {
        background: rgba(109, 74, 255, .14);
        border-color: #8b72ff;
    }

    [data-theme="dark"] .site-icon {
        background: #302b50;
        color: #a996ff;
    }
</style>


<script>
    /*
    |--------------------------------------------------------------------------
    | Password visibility
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.password-toggle').forEach(function(button) {

        button.addEventListener('click', function() {

            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (input.type === 'password') {

                input.type = 'text';

                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');

            } else {

                input.type = 'password';

                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Select / deselect all sites
    |--------------------------------------------------------------------------
    */

    const selectAllButton = document.getElementById('selectAllSites');

    if (selectAllButton) {

        selectAllButton.addEventListener('click', function() {

            const checkboxes = document.querySelectorAll('.site-checkbox');

            const allSelected = Array.from(checkboxes)
                .every(function(checkbox) {
                    return checkbox.checked;
                });

            checkboxes.forEach(function(checkbox) {
                checkbox.checked = !allSelected;
            });

            this.textContent = allSelected ?
                'Select All' :
                'Deselect All';

        });

    }
</script>

@endsection
