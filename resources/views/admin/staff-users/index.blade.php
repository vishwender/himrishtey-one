@extends('admin.layout')

@section('title', 'Staff Users')

@section('content')

<div class="content">

    {{-- ================================================================
        Header
    ================================================================= --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="mb-1">
                Staff Users
            </h1>

            <p class="text-muted mb-0">
                Manage HimRishtey staff accounts, roles and site access.
            </p>
        </div>

        <a
            href="{{ route('admin.staff-users.create') }}"
            class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Add Staff User
        </a>

    </div>


    {{-- ================================================================
        Alerts
    ================================================================= --}}

    @if(session('success'))

    <div
        class="alert alert-success alert-dismissible fade show mb-4"
        role="alert">

        <i class="bi bi-check-circle me-2"></i>

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"></button>

    </div>

    @endif


    @if(session('error'))

    <div
        class="alert alert-danger alert-dismissible fade show mb-4"
        role="alert">

        <i class="bi bi-exclamation-circle me-2"></i>

        {{ session('error') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"></button>

    </div>

    @endif


    {{-- ================================================================
        Staff Users
    ================================================================= --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <div>

                    <h5 class="mb-1">
                        All Staff Users
                    </h5>

                    <div class="text-muted small">

                        {{ number_format($staffUsers->total()) }}

                        {{ $staffUsers->total() === 1 ? 'staff user' : 'staff users' }}

                    </div>

                </div>


                {{-- Search --}}

                <form
                    method="GET"
                    action="{{ route('admin.staff-users.index') }}"
                    class="staff-search-form">

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ $search ?? '' }}"
                            placeholder="Search staff users...">

                        @if(!empty($search))

                        <a
                            href="{{ route('admin.staff-users.index') }}"
                            class="btn btn-outline-secondary"
                            title="Clear search">
                            <i class="bi bi-x-lg"></i>
                        </a>

                        @endif

                        <button
                            type="submit"
                            class="btn btn-primary">
                            Search
                        </button>

                    </div>

                </form>

            </div>

        </div>


        <div class="card-body p-0">

            @if($staffUsers->count())

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0 staff-table">

                    <thead class="table-light">

                        <tr>

                            <th class="ps-4">
                                Staff User
                            </th>

                            <th>
                                Profile ID
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Site Access
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="text-end pe-4">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($staffUsers as $staff)

                        <tr>

                            {{-- ====================================================
                                        Staff
                                    ===================================================== --}}

                            <td class="ps-4">

                                <div class="d-flex align-items-center">

                                    <div class="staff-avatar me-3">

                                        {{
                                                    strtoupper(
                                                        substr(
                                                            trim($staff->name),
                                                            0,
                                                            1
                                                        )
                                                    )
                                                }}

                                    </div>


                                    <div>

                                        <div class="fw-semibold">
                                            {{ $staff->name }}
                                        </div>

                                        <div class="small text-muted">
                                            {{ $staff->email }}
                                        </div>

                                    </div>

                                </div>

                            </td>


                            {{-- ====================================================
                                        Profile ID
                                    ===================================================== --}}

                            <td>

                                @if(!empty($staff->profile_id))

                                <span class="fw-semibold">
                                    {{ $staff->profile_id }}
                                </span>

                                @else

                                <span class="text-muted">
                                    —
                                </span>

                                @endif

                            </td>


                            {{-- ====================================================
                                        Role
                                    ===================================================== --}}

                            <td>

                                <div class="d-flex flex-wrap gap-1">

                                    @forelse($staff->roles as $role)

                                    <span class="badge bg-primary-subtle text-primary">

                                        {{ $role->name }}

                                    </span>

                                    @empty

                                    <span class="text-muted">
                                        No role
                                    </span>

                                    @endforelse

                                </div>

                            </td>


                            {{-- ====================================================
                                        Site Access
                                    ===================================================== --}}

                            <td>

                                @if($staff->hasRole('super-admin'))

                                <span class="badge bg-primary-subtle text-primary">

                                    <i class="bi bi-globe2 me-1"></i>

                                    All Sites

                                </span>

                                @elseif($staff->sites->count())

                                <div class="d-flex flex-wrap gap-1">

                                    @foreach($staff->sites as $site)

                                    <span class="badge bg-light text-dark border">

                                        {{ $site->name }}

                                    </span>

                                    @endforeach

                                </div>

                                @else

                                <span class="text-muted">
                                    No site access
                                </span>

                                @endif

                            </td>


                            {{-- ====================================================
                                        Status
                                    ===================================================== --}}

                            <td>

                                @if($staff->status)

                                <span class="badge bg-success-subtle text-success">

                                    <i class="bi bi-check-circle me-1"></i>

                                    Active

                                </span>

                                @else

                                <span class="badge bg-danger-subtle text-danger">

                                    <i class="bi bi-x-circle me-1"></i>

                                    Inactive

                                </span>

                                @endif

                            </td>


                            {{-- ====================================================
                                        Actions
                                    ===================================================== --}}

                            <td class="text-end pe-4">

                                <div class="d-inline-flex align-items-center gap-1">


                                    {{-- Activity --}}

                                    <a
                                        href="{{ route('admin.staff.activity', $staff->id) }}"
                                        class="btn btn-sm btn-outline-secondary"
                                        title="View Activity">

                                        <i class="bi bi-clock-history"></i>

                                    </a>


                                    {{-- Edit --}}

                                    <a
                                        href="{{ route('admin.staff-users.edit', $staff) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit Staff User">

                                        <i class="bi bi-pencil"></i>

                                    </a>


                                    {{-- Toggle Status --}}

                                    <form
                                        action="{{ route('admin.staff-users.toggle-status', $staff) }}"
                                        method="POST"
                                        class="d-inline">

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-warning"
                                            title="{{ $staff->status ? 'Deactivate' : 'Activate' }}"
                                            onclick="return confirm('Are you sure you want to {{ $staff->status ? 'deactivate' : 'activate' }} this staff user?')">

                                            @if($staff->status)

                                            <i class="bi bi-person-dash"></i>

                                            @else

                                            <i class="bi bi-person-check"></i>

                                            @endif

                                        </button>

                                    </form>


                                    {{-- Delete --}}

                                    <form
                                        action="{{ route('admin.staff-users.destroy', $staff) }}"
                                        method="POST"
                                        class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete Staff User"
                                            onclick="return confirm('Are you sure you want to permanently delete this staff user?')">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- ====================================================
                    Pagination
                ===================================================== --}}

            @if($staffUsers->hasPages())

            <div class="card-footer bg-white">

                {{
                            $staffUsers->links(
                                'pagination::bootstrap-5'
                            )
                        }}

            </div>

            @endif


            @else

            {{-- ====================================================
                    Empty State
                ===================================================== --}}

            <div class="text-center py-5">

                <div class="empty-state-icon mb-3">

                    <i class="bi bi-people"></i>

                </div>

                <h5>
                    No Staff Users Found
                </h5>

                <p class="text-muted mb-4">

                    @if(!empty($search))

                    No staff users matched your search.

                    @else

                    There are currently no staff users in the system.

                    @endif

                </p>


                @if(!empty($search))

                <a
                    href="{{ route('admin.staff-users.index') }}"
                    class="btn btn-outline-secondary me-2">

                    <i class="bi bi-arrow-left me-1"></i>

                    Clear Search

                </a>

                @endif


                <a
                    href="{{ route('admin.staff-users.create') }}"
                    class="btn btn-primary">

                    <i class="bi bi-plus-lg me-1"></i>

                    Add Staff User

                </a>

            </div>

            @endif

        </div>

    </div>

</div>


<style>
    /*
    |--------------------------------------------------------------------------
    | Staff Avatar
    |--------------------------------------------------------------------------
    */

    .staff-avatar {

        width: 42px;
        height: 42px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 12px;

        background:
            linear-gradient(135deg,
                #8063ff,
                #6040ed);

        color: #fff;

        font-family: 'Outfit', sans-serif;

        font-weight: 700;

        box-shadow:
            0 5px 12px rgba(96, 64, 237, .18);

    }


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    .staff-search-form {

        width: 100%;
        max-width: 430px;

    }


    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    .staff-table th {

        font-size: .78rem;

        font-weight: 700;

        text-transform: uppercase;

        letter-spacing: .03em;

        color: #6c757d;

        white-space: nowrap;

    }


    .staff-table td {

        vertical-align: middle;

    }


    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    .empty-state-icon {

        width: 64px;
        height: 64px;

        margin-left: auto;
        margin-right: auto;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 16px;

        background: #eeeaff;

        color: var(--app-primary);

        font-size: 1.5rem;

    }


    /*
    |--------------------------------------------------------------------------
    | Mobile
    |--------------------------------------------------------------------------
    */

    @media (max-width: 767.98px) {

        .content h1 {
            font-size: 1.6rem;
        }


        .staff-search-form {
            max-width: 100%;
        }


        .staff-table {
            min-width: 950px;
        }

    }
</style>

@endsection
