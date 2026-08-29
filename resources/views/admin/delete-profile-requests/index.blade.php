@extends('admin.layout')

@section('title', 'Profile Delete Requests')

@section('content')

<div class="container-fluid">

    {{-- ================================================================
         HEADER
    ================================================================= --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h4 class="mb-1">
                Profile Delete Requests
            </h4>

            <div class="text-muted">
                Review and process member profile deletion requests.
            </div>
        </div>

        <a
            href="{{ route('admin.members.index') }}"
            class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i>
            Members
        </a>

    </div>


    {{-- ================================================================
         FLASH MESSAGES
    ================================================================= --}}

    @if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        <i class="bi bi-check-circle me-2"></i>

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"></button>

    </div>

    @endif


    @if(session('error'))

    <div class="alert alert-danger alert-dismissible fade show">

        <i class="bi bi-exclamation-circle me-2"></i>

        {{ session('error') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"></button>

    </div>

    @endif


    @if($errors->any())

    <div class="alert alert-danger">

        <ul class="mb-0">

            @foreach($errors->all() as $error)

            <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

    @endif


    {{-- ================================================================
         SUMMARY
    ================================================================= --}}

    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small">
                        Total
                    </div>

                    <div class="fs-4 fw-bold">
                        {{ number_format($totalCount) }}
                    </div>

                </div>

            </div>

        </div>


        <div class="col-6 col-md-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small">
                        Pending
                    </div>

                    <div class="fs-4 fw-bold text-warning">
                        {{ number_format($pendingCount) }}
                    </div>

                </div>

            </div>

        </div>


        <div class="col-6 col-md-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small">
                        Accepted
                    </div>

                    <div class="fs-4 fw-bold text-success">
                        {{ number_format($acceptedCount) }}
                    </div>

                </div>

            </div>

        </div>


        <div class="col-6 col-md-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small">
                        Rejected
                    </div>

                    <div class="fs-4 fw-bold text-danger">
                        {{ number_format($rejectedCount) }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================
         FILTERS
    ================================================================= --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('admin.delete-profile-requests.index') }}">

                <div class="row g-3 align-items-end">

                    <div class="col-md-5">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ $search }}"
                            placeholder="Profile ID, name or mobile...">

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option
                                value="all"
                                {{ $status === 'all' ? 'selected' : '' }}>
                                All
                            </option>

                            <option
                                value="pending"
                                {{ $status === 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option
                                value="accepted"
                                {{ $status === 'accepted' ? 'selected' : '' }}>
                                Accepted
                            </option>

                            <option
                                value="rejected"
                                {{ $status === 'rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Per Page
                        </label>

                        <select
                            name="per_page"
                            class="form-select">

                            @foreach([10, 25, 50, 100] as $size)

                            <option
                                value="{{ $size }}"
                                {{ (int) $perPage === $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- ================================================================
         BULK FORM
    ================================================================= --}}

    <form
        method="POST"
        action="{{ route('admin.delete-profile-requests.bulk-action') }}"
        id="bulkDeleteRequestForm">

        @csrf


        {{-- ============================================================
             BULK TOOLBAR
        ============================================================= --}}

        <div
            id="bulkToolbar"
            class="card border-0 shadow-sm mb-3 d-none">

            <div class="card-body py-3">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                    <div>

                        <strong id="selectedRequestCount">
                            0
                        </strong>

                        request(s) selected

                    </div>


                    <div class="d-flex gap-2">

                        @if(auth('admin')->user()?->hasPermission('bulk-profile-delete-requests'))

                        <button
                            type="button"
                            class="btn btn-success"
                            onclick="submitBulkDeleteAction('accept')">
                            <i class="bi bi-check-circle me-1"></i>
                            Accept Selected
                        </button>


                        <button
                            type="button"
                            class="btn btn-danger"
                            onclick="submitBulkDeleteAction('reject')">
                            <i class="bi bi-x-circle me-1"></i>
                            Reject Selected
                        </button>

                        @endif

                    </div>

                </div>

            </div>

        </div>


        <input
            type="hidden"
            name="action"
            id="bulkAction"
            value="">


        {{-- ============================================================
             TABLE
        ============================================================= --}}

        <div class="card border-0 shadow-sm">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th style="width: 45px;">

                                @if($status === 'pending' || $status === 'all')

                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="selectAllDeleteRequests">

                                @endif

                            </th>

                            <th>Member</th>

                            <th>Requested By</th>

                            <th>Reason</th>

                            <th>Requests</th>

                            <th>Date</th>

                            <th>Status</th>

                            <th class="text-end">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($requests as $deleteRequest)

                        @php

                        $member = $deleteRequest->member;

                        $requestedBy =
                        $admins[$deleteRequest->request_by] ?? null;

                        @endphp


                        <tr>

                            {{-- Checkbox --}}

                            <td>

                                @if((int) $deleteRequest->status === 0)

                                <input
                                    type="checkbox"
                                    name="request_ids[]"
                                    value="{{ $deleteRequest->id }}"
                                    class="form-check-input delete-request-checkbox">

                                @endif

                            </td>


                            {{-- Member --}}

                            <td>

                                @if($member)

                                <div class="fw-semibold">
                                    {{ $member->full_name }}
                                </div>

                                <div class="small text-muted">
                                    {{ $member->profile_id }}
                                </div>

                                @else

                                <span class="text-muted">
                                    Member unavailable
                                </span>

                                @endif

                            </td>


                            {{-- Requested by --}}

                            <td>

                                @if($requestedBy)

                                <div class="fw-semibold">
                                    {{ $requestedBy->name }}
                                </div>

                                <div class="small text-muted">
                                    {{ $requestedBy->profile_id }}
                                </div>

                                @else

                                <span class="text-muted">
                                    Admin #{{ $deleteRequest->request_by }}
                                </span>

                                @endif

                            </td>


                            {{-- Reason --}}

                            <td style="max-width: 300px;">

                                <div
                                    class="text-wrap"
                                    title="{{ $deleteRequest->reason }}">
                                    {{ \Illuminate\Support\Str::limit(
                                            $deleteRequest->reason,
                                            100
                                        ) }}
                                </div>

                            </td>


                            {{-- Count --}}

                            <td>

                                <span class="badge bg-light text-dark border">
                                    {{ $deleteRequest->request_count ?? 1 }}
                                </span>

                            </td>


                            {{-- Date --}}

                            <td>

                                {{ $deleteRequest->date ?: '-' }}

                            </td>


                            {{-- Status --}}

                            <td>

                                @if((int) $deleteRequest->status === 0)

                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>

                                @elseif((int) $deleteRequest->status === 1)

                                <span class="badge bg-success">
                                    Accepted
                                </span>

                                @else

                                <span class="badge bg-danger">
                                    Rejected
                                </span>

                                @endif

                            </td>


                            {{-- Action --}}

                            <td class="text-end">

                                @if((int) $deleteRequest->status === 0)

                                <div class="d-flex justify-content-end gap-2">

                                    @if(auth('admin')->user()?->hasPermission('approve-profile-delete-request'))

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-success"
                                        onclick="submitSingleRequest(
                                                        'acceptForm{{ $deleteRequest->id }}',
                                                        'Accept this profile delete request?'
                                                    )">
                                        <i class="bi bi-check-lg"></i>
                                    </button>

                                    @endif


                                    @if(auth('admin')->user()?->hasPermission('reject-profile-delete-request'))

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="submitSingleRequest(
                                                        'rejectForm{{ $deleteRequest->id }}',
                                                        'Reject this profile delete request?'
                                                    )">
                                        <i class="bi bi-x-lg"></i>
                                    </button>

                                    @endif

                                </div>

                                @else

                                <span class="text-muted small">
                                    Processed
                                </span>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td
                                colspan="8"
                                class="text-center py-5 text-muted">

                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                No profile delete requests found.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            @if($requests->hasPages())

            <div class="card-footer bg-white">

                {{ $requests->links() }}

            </div>

            @endif

        </div>

    </form>


    {{-- ================================================================
         INDIVIDUAL FORMS
    ================================================================= --}}

    @foreach($requests as $deleteRequest)

    @if((int) $deleteRequest->status === 0)

    <form
        method="POST"
        action="{{ route(
                    'admin.delete-profile-requests.accept',
                    $deleteRequest->id
                ) }}"
        id="acceptForm{{ $deleteRequest->id }}"
        class="d-none">
        @csrf
    </form>


    <form
        method="POST"
        action="{{ route(
                    'admin.delete-profile-requests.reject',
                    $deleteRequest->id
                ) }}"
        id="rejectForm{{ $deleteRequest->id }}"
        class="d-none">
        @csrf
    </form>

    @endif

    @endforeach

</div>

@endsection


@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const selectAll =
            document.getElementById(
                'selectAllDeleteRequests'
            );

        const checkboxes =
            document.querySelectorAll(
                '.delete-request-checkbox'
            );


        /*
        |--------------------------------------------------------------------------
        | Select All
        |--------------------------------------------------------------------------
        */

        if (selectAll) {

            selectAll.addEventListener(
                'change',
                function() {

                    checkboxes.forEach(function(checkbox) {

                        checkbox.checked =
                            selectAll.checked;

                    });


                    updateBulkToolbar();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Individual checkbox
        |--------------------------------------------------------------------------
        */

        checkboxes.forEach(function(checkbox) {

            checkbox.addEventListener(
                'change',
                function() {

                    updateSelectAllState();

                    updateBulkToolbar();

                }
            );

        });


        updateBulkToolbar();

    });


    /*
    |--------------------------------------------------------------------------
    | Update Select All
    |--------------------------------------------------------------------------
    */

    function updateSelectAllState() {
        const selectAll =
            document.getElementById(
                'selectAllDeleteRequests'
            );

        if (!selectAll) {
            return;
        }


        const checkboxes =
            Array.from(
                document.querySelectorAll(
                    '.delete-request-checkbox'
                )
            );


        const checked =
            checkboxes.filter(function(checkbox) {

                return checkbox.checked;

            });


        selectAll.checked =
            checkboxes.length > 0 &&
            checked.length === checkboxes.length;


        selectAll.indeterminate =
            checked.length > 0 &&
            checked.length < checkboxes.length;
    }


    /*
    |--------------------------------------------------------------------------
    | Bulk Toolbar
    |--------------------------------------------------------------------------
    */

    function updateBulkToolbar() {
        const selected =
            document.querySelectorAll(
                '.delete-request-checkbox:checked'
            );


        const toolbar =
            document.getElementById(
                'bulkToolbar'
            );


        const counter =
            document.getElementById(
                'selectedRequestCount'
            );


        if (!toolbar || !counter) {
            return;
        }


        counter.textContent =
            selected.length;


        if (selected.length > 0) {

            toolbar.classList.remove('d-none');

        } else {

            toolbar.classList.add('d-none');

        }
    }


    /*
    |--------------------------------------------------------------------------
    | Bulk Action
    |--------------------------------------------------------------------------
    */

    function submitBulkDeleteAction(action) {
        const selected =
            document.querySelectorAll(
                '.delete-request-checkbox:checked'
            );


        if (selected.length === 0) {

            alert(
                'Please select at least one delete request.'
            );

            return;
        }


        let message;


        if (action === 'accept') {

            message =
                'Are you sure you want to ACCEPT ' +
                selected.length +
                ' profile delete request(s)?';

        } else {

            message =
                'Are you sure you want to REJECT ' +
                selected.length +
                ' profile delete request(s)?';

        }


        if (!confirm(message)) {
            return;
        }


        document.getElementById(
            'bulkAction'
        ).value = action;


        document.getElementById(
            'bulkDeleteRequestForm'
        ).submit();
    }


    /*
    |--------------------------------------------------------------------------
    | Single Action
    |--------------------------------------------------------------------------
    */

    function submitSingleRequest(
        formId,
        message
    ) {

        if (!confirm(message)) {
            return;
        }


        const form =
            document.getElementById(formId);


        if (form) {
            form.submit();
        }
    }
</script>

@endpush