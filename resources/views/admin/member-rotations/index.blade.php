@extends('admin.layout')

@section('title', 'Member Rotations')

@section('content')

@php

$currentAdmin = auth('admin')->user();

/*
|--------------------------------------------------------------------------
| Admin Permissions
|--------------------------------------------------------------------------
*/

$canViewAllRotations = $currentAdmin
? $currentAdmin->hasPermission('view-all-rotations')
: false;

$canViewOwnRotations = $currentAdmin
? $currentAdmin->hasPermission('view-own-rotations')
: false;

$canCreateRotations = $currentAdmin
? $currentAdmin->hasPermission('create-rotations')
: false;

$canEditRotations = $currentAdmin
? $currentAdmin->hasPermission('edit-rotations')
: false;

$canCompleteRotations = $currentAdmin
? $currentAdmin->hasPermission('complete-rotations')
: false;

$canCancelRotations = $currentAdmin
? $currentAdmin->hasPermission('cancel-rotations')
: false;

@endphp


<div class="container-fluid">

    {{-- =========================================================
         Header
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="h3 mb-1">
                Member Rotations
            </h1>

            <p class="text-muted mb-0">
                Manage and monitor scheduled member rotations.
            </p>

        </div>

    </div>


    {{-- =========================================================
         Access Information
    ========================================================== --}}

    <div class="mb-4">

        @if($canViewAllRotations)

        <div class="alert alert-info d-flex align-items-center mb-0">

            <i class="bi bi-shield-check fs-5 me-2"></i>

            <div>

                <strong>All Rotations</strong>

                <div class="small">
                    You have permission to view rotations assigned to all administrators.
                </div>

            </div>

        </div>

        @elseif($canViewOwnRotations)

        <div class="alert alert-light border d-flex align-items-center mb-0">

            <i class="bi bi-person-check fs-5 me-2"></i>

            <div>

                <strong>My Rotations</strong>

                <div class="small text-muted">
                    You are viewing only rotations assigned to you.
                </div>

            </div>

        </div>

        @endif

    </div>


    {{-- =========================================================
         Summary Cards
    ========================================================== --}}

    <div class="row g-3 mb-4">


        {{-- =====================================================
             Total
        ====================================================== --}}

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small">
                                Total Rotations
                            </div>

                            <h3 class="mb-0">
                                {{ $totalRotations }}
                            </h3>

                        </div>

                        <div class="text-primary fs-3">

                            <i class="bi bi-arrow-repeat"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             Today
        ====================================================== --}}

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small">
                                Today
                            </div>

                            <h3 class="mb-0">
                                {{ $todayRotations }}
                            </h3>

                        </div>

                        <div class="text-danger fs-3">

                            <i class="bi bi-calendar-event"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             Tomorrow
        ====================================================== --}}

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small">
                                Tomorrow
                            </div>

                            <h3 class="mb-0">
                                {{ $tomorrowRotations }}
                            </h3>

                        </div>

                        <div class="text-warning fs-3">

                            <i class="bi bi-calendar-plus"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             Next 2 Days
        ====================================================== --}}

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small">
                                Next 2 Days
                            </div>

                            <h3 class="mb-0">
                                {{ $nextTwoDaysRotations }}
                            </h3>

                        </div>

                        <div class="text-info fs-3">

                            <i class="bi bi-clock-history"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         Rotations Table
    ========================================================== --}}

    <div class="card border-0 shadow-sm">


        {{-- =====================================================
             Card Header
        ====================================================== --}}

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="mb-0">
                        Scheduled Rotations
                    </h5>

                    <small class="text-muted">
                        Upcoming member rotations
                    </small>

                </div>


                <div>

                    <span class="badge bg-light text-dark border">

                        {{ $totalRotations }}

                        {{ $totalRotations == 1 ? 'Rotation' : 'Rotations' }}

                    </span>

                </div>

            </div>

        </div>


        {{-- =====================================================
             Table
        ====================================================== --}}

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>
                                Member
                            </th>

                            <th>
                                Profile ID
                            </th>

                            <th>
                                Assigned To
                            </th>

                            <th>
                                Rotation
                            </th>

                            <th>
                                Days
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="text-end">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        @forelse($rotations as $rotation)

                        @php

                        /*
                        |--------------------------------------------------------------------------
                        | Rotation Date
                        |--------------------------------------------------------------------------
                        */

                        $rotationDate = $rotation->next_rotation_at;


                        /*
                        |--------------------------------------------------------------------------
                        | Date Status
                        |--------------------------------------------------------------------------
                        */

                        $isToday =
                        $rotationDate &&
                        $rotationDate->isToday();


                        $isTomorrow =
                        $rotationDate &&
                        $rotationDate->isTomorrow();


                        $isOverdue =
                        $rotationDate &&
                        $rotationDate->isPast() &&
                        !$isToday &&
                        !$rotation->completed_at;


                        /*
                        |--------------------------------------------------------------------------
                        | Assigned Admin
                        |--------------------------------------------------------------------------
                        |
                        | Admins live in the CENTRAL database.
                        |
                        | Therefore we use the $admins collection
                        | supplied by the controller.
                        |
                        */

                        $assignedAdmin = null;

                        if ($rotation->user_id) {

                        $assignedAdmin =
                        $admins[$rotation->user_id] ?? null;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Status
                        |--------------------------------------------------------------------------
                        */

                        $status = strtolower(
                        trim(
                        $rotation->status ?? 'pending'
                        )
                        );

                        @endphp


                        <tr>


                            {{-- =================================================
                                 Member
                            ================================================== --}}

                            <td>

                                @if($rotation->member)

                                <div class="d-flex align-items-center">

                                    <div
                                        class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2 flex-shrink-0"
                                        style="width:40px;height:40px;">

                                        <i class="bi bi-person"></i>

                                    </div>


                                    <div>

                                        <div class="fw-semibold">

                                            {{ $rotation->member->full_name }}

                                        </div>


                                        <small class="text-muted">

                                            {{ $rotation->member->mobile_number }}

                                        </small>

                                    </div>

                                </div>

                                @else

                                <div class="d-flex align-items-center">

                                    <div
                                        class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center me-2 flex-shrink-0"
                                        style="width:40px;height:40px;">

                                        <i class="bi bi-person-x"></i>

                                    </div>


                                    <div>

                                        <div class="fw-semibold text-muted">

                                            Member Removed

                                        </div>


                                        <small class="text-muted">

                                            Member ID:
                                            {{ $rotation->member_id }}

                                        </small>

                                    </div>

                                </div>

                                @endif

                            </td>


                            {{-- =================================================
                                 Profile ID
                            ================================================== --}}

                            <td>

                                @if($rotation->member)

                                <strong>
                                    {{ $rotation->member->profile_id }}
                                </strong>

                                @else

                                <span class="text-muted">
                                    -
                                </span>

                                @endif

                            </td>


                            {{-- =================================================
                                 Assigned Admin
                            ================================================== --}}

                            <td>

                                @if($assignedAdmin)

                                <div class="d-flex align-items-center">

                                    <div
                                        class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center me-2 flex-shrink-0"
                                        style="width:36px;height:36px;">

                                        <i class="bi bi-person-badge"></i>

                                    </div>


                                    <div>

                                        <div class="fw-semibold">

                                            {{ $assignedAdmin->name }}

                                        </div>


                                        <small class="text-muted">

                                            {{ $assignedAdmin->email }}

                                        </small>

                                    </div>

                                </div>

                                @else

                                <span class="text-muted">
                                    Unassigned
                                </span>

                                @endif

                            </td>


                            {{-- =================================================
                                 Rotation Date
                            ================================================== --}}

                            <td>

                                @if($rotationDate)

                                <div class="fw-semibold">

                                    {{ $rotationDate->format('d M Y') }}

                                </div>


                                <small class="text-muted">

                                    {{ $rotationDate->format('h:i A') }}

                                </small>


                                <div class="mt-1">

                                    @if($rotation->completed_at)

                                    <span class="badge bg-success">
                                        Completed
                                    </span>

                                    @elseif($isOverdue)

                                    <span class="badge bg-dark">
                                        Overdue
                                    </span>

                                    @elseif($isToday)

                                    <span class="badge bg-danger">
                                        Today
                                    </span>

                                    @elseif($isTomorrow)

                                    <span class="badge bg-warning text-dark">
                                        Tomorrow
                                    </span>

                                    @else

                                    <span class="badge bg-light text-dark border">
                                        Upcoming
                                    </span>

                                    @endif

                                </div>

                                @else

                                <span class="text-muted">
                                    -
                                </span>

                                @endif

                            </td>


                            {{-- =================================================
                                 Days
                            ================================================== --}}

                            <td>

                                @if($rotation->days)

                                {{ $rotation->days }}

                                {{ $rotation->days == 1 ? 'day' : 'days' }}

                                @else

                                <span class="text-muted">
                                    -
                                </span>

                                @endif

                            </td>


                            {{-- =================================================
                                 Status
                            ================================================== --}}

                            <td>

                                @if($status === 'completed')

                                <span class="badge bg-success">
                                    Completed
                                </span>

                                @elseif($status === 'cancelled')

                                <span class="badge bg-secondary">
                                    Cancelled
                                </span>

                                @elseif($status === 'pending')

                                <span class="badge bg-primary">
                                    Pending
                                </span>

                                @elseif($isOverdue)

                                <span class="badge bg-dark">
                                    Overdue
                                </span>

                                @else

                                <span class="badge bg-light text-dark border">

                                    {{ ucfirst($status) }}

                                </span>

                                @endif

                            </td>


                            {{-- =================================================
                                 Actions
                            ================================================== --}}

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


                                    <ul
                                        class="dropdown-menu dropdown-menu-end shadow-sm">


                                        {{-- =====================================
                                             View Member
                                        ====================================== --}}

                                        @if($rotation->member)

                                        <li>

                                            <a
                                                class="dropdown-item"
                                                href="{{ route(
                                                    'admin.members.show',
                                                    $rotation->member->id
                                                ) }}">

                                                <i
                                                    class="bi bi-person me-2 text-primary">
                                                </i>

                                                View Member

                                            </a>

                                        </li>

                                        @endif


                                        {{-- =====================================
                                             Complete Rotation
                                        ====================================== --}}

                                        @if(
                                        $canCompleteRotations &&
                                        !$rotation->completed_at &&
                                        $status !== 'completed' &&
                                        $status !== 'cancelled'
                                        )

                                        <li>

                                            <button
                                                type="button"
                                                class="dropdown-item"
                                                data-rotation-id="{{ $rotation->id }}">

                                                <i
                                                    class="bi bi-check-circle me-2 text-success">
                                                </i>

                                                Complete Rotation

                                            </button>

                                        </li>

                                        @endif


                                        {{-- =====================================
                                             Reschedule / Edit
                                        ====================================== --}}

                                        @if(
                                        $canEditRotations &&
                                        !$rotation->completed_at &&
                                        $status !== 'completed' &&
                                        $status !== 'cancelled'
                                        )

                                        <li>

                                            <button
                                                type="button"
                                                class="dropdown-item"
                                                data-rotation-id="{{ $rotation->id }}">

                                                <i
                                                    class="bi bi-calendar-event me-2 text-warning">
                                                </i>

                                                Reschedule

                                            </button>

                                        </li>

                                        @endif


                                        {{-- =====================================
                                             Cancel Rotation
                                        ====================================== --}}

                                        @if(
                                        $canCancelRotations &&
                                        !$rotation->completed_at &&
                                        $status !== 'completed' &&
                                        $status !== 'cancelled'
                                        )

                                        <li>

                                            <button
                                                type="button"
                                                class="dropdown-item text-danger"
                                                data-rotation-id="{{ $rotation->id }}">

                                                <i
                                                    class="bi bi-x-circle me-2">
                                                </i>

                                                Cancel Rotation

                                            </button>

                                        </li>

                                        @endif


                                        {{-- =====================================
                                             Divider
                                        ====================================== --}}

                                        @if($rotation->member)

                                        <li>

                                            <hr class="dropdown-divider">

                                        </li>

                                        @endif


                                        {{-- =====================================
                                             Edit Member
                                        ====================================== --}}

                                        @if(
                                        $rotation->member &&
                                        $currentAdmin &&
                                        $currentAdmin->hasPermission('edit-members')
                                        )

                                        <li>

                                            <a
                                                class="dropdown-item"
                                                href="{{ route(
                                                    'admin.members.edit',
                                                    $rotation->member->id
                                                ) }}">

                                                <i
                                                    class="bi bi-pencil me-2 text-primary">
                                                </i>

                                                Edit Member

                                            </a>

                                        </li>

                                        @endif

                                    </ul>

                                </div>

                            </td>

                        </tr>


                        @empty

                        {{-- =====================================================
                             Empty State
                        ====================================================== --}}

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5">

                                <div class="text-muted">

                                    <i
                                        class="bi bi-arrow-repeat fs-1 d-block mb-3">
                                    </i>


                                    <h5>
                                        No rotations found
                                    </h5>


                                    <p class="mb-0">

                                        @if($canViewAllRotations)

                                        There are currently no scheduled rotations.

                                        @elseif($canViewOwnRotations)

                                        You currently have no rotations assigned to you.

                                        @else

                                        You do not have permission to view rotations.

                                        @endif

                                    </p>

                                </div>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =========================================================
             Pagination
        ========================================================== --}}

        @if($rotations->hasPages())

        <div class="card-footer bg-white">

            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">


                {{-- Pagination Information --}}

                <div class="text-muted small">

                    Showing

                    <strong>
                        {{ $rotations->firstItem() }}
                    </strong>

                    to

                    <strong>
                        {{ $rotations->lastItem() }}
                    </strong>

                    of

                    <strong>
                        {{ $rotations->total() }}
                    </strong>

                    rotations

                </div>


                {{-- Pagination Links --}}

                <div>

                    {{ $rotations->onEachSide(1)->links() }}

                </div>

            </div>

        </div>

        @endif

    </div>

</div>

@endsection