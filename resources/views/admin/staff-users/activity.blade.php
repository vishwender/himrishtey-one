@extends('admin.layout')

@section('title', 'Staff Activity')

@section('content')

<div class="content">

    {{-- ================================================================
        Back
    ================================================================= --}}

    <div class="mb-3">

        <a
            href="{{ route('admin.staff-users.index') }}"
            class="text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Staff Users
        </a>

    </div>


    {{-- ================================================================
        Staff Header
    ================================================================= --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex flex-wrap align-items-center gap-3">

                <div class="staff-avatar">

                    {{
                        strtoupper(
                            substr(
                                trim($admin->name),
                                0,
                                1
                            )
                        )
                    }}

                </div>


                <div class="flex-grow-1">

                    <h4 class="fw-bold mb-1">
                        {{ $admin->name }}
                    </h4>

                    <div class="text-muted">

                        {{ $admin->email }}

                        @if(!empty($admin->profile_id))

                        <span class="mx-2">•</span>

                        {{ $admin->profile_id }}

                        @endif

                    </div>

                </div>


                <div>

                    @if($admin->status)

                    <span class="badge bg-success-subtle text-success px-3 py-2">

                        <i class="bi bi-check-circle me-1"></i>
                        Active

                    </span>

                    @else

                    <span class="badge bg-danger-subtle text-danger px-3 py-2">

                        <i class="bi bi-x-circle me-1"></i>
                        Inactive

                    </span>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================
        Filters
    ================================================================= --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET">

                <div class="row g-3 align-items-end">

                    {{-- Site --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label fw-semibold">
                            Site
                        </label>

                        <select
                            name="site_id"
                            class="form-select">

                            <option value="">
                                All Sites
                            </option>

                            @foreach($sites as $site)

                            <option
                                value="{{ $site->id }}"
                                @selected(
                                (string) request('site_id')===(string) $site->id
                                )
                                >
                                {{ $site->name }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Activity --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label fw-semibold">
                            Activity
                        </label>

                        <select
                            name="action"
                            class="form-select">

                            <option value="">
                                All Activities
                            </option>

                            @foreach($actions as $action)

                            <option
                                value="{{ $action }}"
                                @selected(
                                request('action')===$action
                                )>

                                {{
                                        ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $action
                                            )
                                        )
                                    }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- From --}}

                    <div class="col-lg-2 col-md-6">

                        <label class="form-label fw-semibold">
                            From
                        </label>

                        <input
                            type="date"
                            name="date_from"
                            value="{{ request('date_from') }}"
                            class="form-control">

                    </div>


                    {{-- To --}}

                    <div class="col-lg-2 col-md-6">

                        <label class="form-label fw-semibold">
                            To
                        </label>

                        <input
                            type="date"
                            name="date_to"
                            value="{{ request('date_to') }}"
                            class="form-control">

                    </div>


                    {{-- Buttons --}}

                    <div class="col-lg-2">

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary">
                                <i class="bi bi-funnel"></i>
                            </button>

                            <a
                                href="{{ route('admin.staff.activity', $admin->id) }}"
                                class="btn btn-outline-secondary"
                                title="Clear Filters">
                                <i class="bi bi-x-lg"></i>
                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- ================================================================
        Activity History
    ================================================================= --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="fw-bold mb-1">
                        Activity History
                    </h5>

                    <div class="small text-muted">

                        {{ number_format($activities->total()) }}

                        activities found

                    </div>

                </div>

            </div>

        </div>


        <div class="card-body p-0">

            @if($activities->count())

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0 activity-table">

                    <thead class="table-light">

                        <tr>

                            <th class="ps-4">
                                Activity
                            </th>

                            <th>
                                Member
                            </th>

                            <th>
                                Details
                            </th>

                            <th>
                                Site
                            </th>

                            <th>
                                IP Address
                            </th>

                            <th class="pe-4">
                                Date & Time
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($activities as $activity)

                        @php

                        $metadata =
                        $activity->metadata ?? [];

                        $profileId =
                        $metadata['profile_id']
                        ?? null;


                        /*
                        |--------------------------------------------------------------------------
                        | Human Readable Activity
                        |--------------------------------------------------------------------------
                        */

                        $activityName = match($activity->action) {

                        'login' =>
                        'Logged In',

                        'logout' =>
                        'Logged Out',

                        'member_viewed' =>
                        'Viewed Profile',

                        'remarks_updated' =>
                        'Updated Remarks',

                        'member_updated' =>
                        'Edited Profile',

                        'member_status_changed' =>
                        'Changed Status',

                        'member_visibility_changed' =>
                        'Changed Visibility',

                        'member_trusted_changed' =>
                        'Changed Trusted Status',

                        'member_promoted_changed' =>
                        'Changed Promotion',

                        default =>
                        ucwords(
                        str_replace(
                        '_',
                        ' ',
                        $activity->action
                        )
                        ),
                        };


                        /*
                        |--------------------------------------------------------------------------
                        | Icon
                        |--------------------------------------------------------------------------
                        */

                        $activityIcon = match($activity->action) {

                        'login' =>
                        'bi-box-arrow-in-right',

                        'logout' =>
                        'bi-box-arrow-right',

                        'member_viewed' =>
                        'bi-eye',

                        'remarks_updated' =>
                        'bi-chat-left-text',

                        'member_updated' =>
                        'bi-pencil-square',

                        'member_status_changed' =>
                        'bi-person-check',

                        'member_visibility_changed' =>
                        'bi-eye-slash',

                        'member_trusted_changed' =>
                        'bi-patch-check',

                        'member_promoted_changed' =>
                        'bi-star',

                        default =>
                        'bi-clock-history',
                        };

                        @endphp


                        <tr>

                            {{-- Activity --}}

                            <td class="ps-4">

                                <div class="d-flex align-items-center gap-2">

                                    <div class="activity-icon">

                                        <i class="bi {{ $activityIcon }}"></i>

                                    </div>

                                    <div class="fw-semibold">

                                        {{ $activityName }}

                                    </div>

                                </div>

                            </td>


                            {{-- Member --}}

                            <td>

                                @if($activity->member_id)

                                @if($profileId)

                                <div class="fw-semibold">
                                    {{ $profileId }}
                                </div>

                                @endif

                                <div class="small text-muted">

                                    ID:
                                    {{ $activity->member_id }}

                                </div>

                                @else

                                <span class="text-muted">
                                    —
                                </span>

                                @endif

                            </td>


                            {{-- Details --}}

                            <td class="activity-details">

                                <div>
                                    {{ $activity->description ?: '—' }}
                                </div>


                                {{-- Old -> New value --}}

                                @if(
                                array_key_exists(
                                'old_value',
                                $metadata
                                ) &&
                                array_key_exists(
                                'new_value',
                                $metadata
                                )
                                )

                                <div class="small mt-1">

                                    <span class="text-muted">

                                        {{
                                                        $metadata['old_value']
                                                        === ''
                                                            ? 'Empty'
                                                            : $metadata['old_value']
                                                    }}

                                    </span>

                                    <i class="bi bi-arrow-right mx-2 text-muted"></i>

                                    <span class="fw-semibold">

                                        {{
                                                        $metadata['new_value']
                                                        === ''
                                                            ? 'Empty'
                                                            : $metadata['new_value']
                                                    }}

                                    </span>

                                </div>

                                @endif


                                {{-- Member Edit Changes --}}

                                @if(
                                $activity->action === 'member_updated' &&
                                !empty($metadata['changes'])
                                )

                                <div class="activity-changes mt-2">

                                    @foreach(
                                    $metadata['changes']
                                    as $field => $change
                                    )

                                    <div class="small mb-1">

                                        <strong>

                                            {{
                                                                ucwords(
                                                                    str_replace(
                                                                        '_',
                                                                        ' ',
                                                                        $field
                                                                    )
                                                                )
                                                            }}:

                                        </strong>


                                        <span class="text-muted">

                                            {{
                                                                \Illuminate\Support\Str::limit(
                                                                    (string) (
                                                                        $change['old']
                                                                        ?? '—'
                                                                    ),
                                                                    60
                                                                )
                                                            }}

                                        </span>


                                        <i class="bi bi-arrow-right mx-1"></i>


                                        <span>

                                            {{
                                                                \Illuminate\Support\Str::limit(
                                                                    (string) (
                                                                        $change['new']
                                                                        ?? '—'
                                                                    ),
                                                                    60
                                                                )
                                                            }}

                                        </span>

                                    </div>

                                    @endforeach

                                </div>

                                @endif

                            </td>


                            {{-- Site --}}

                            <td>

                                @if($activity->site)

                                <div class="fw-semibold">
                                    {{ $activity->site->name }}
                                </div>

                                @else

                                <span class="text-muted">
                                    —
                                </span>

                                @endif

                            </td>


                            {{-- IP --}}

                            <td>

                                <span class="small font-monospace">

                                    {{ $activity->ip_address ?: '—' }}

                                </span>

                            </td>


                            {{-- Date --}}

                            <td class="pe-4 text-nowrap">

                                @if($activity->created_at)

                                <div class="fw-semibold">

                                    {{
                                                    $activity->created_at
                                                        ->format('d M Y')
                                                }}

                                </div>

                                <div class="small text-muted">

                                    {{
                                                    $activity->created_at
                                                        ->format('h:i A')
                                                }}

                                </div>

                                @else

                                —

                                @endif

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}

            @if($activities->hasPages())

            <div class="card-footer bg-white">

                {{
                            $activities->links(
                                'pagination::bootstrap-5'
                            )
                        }}

            </div>

            @endif


            @else

            <div class="text-center py-5">

                <div class="empty-state-icon mb-3">

                    <i class="bi bi-clock-history"></i>

                </div>

                <h5>
                    No Activity Found
                </h5>

                <p class="text-muted mb-0">

                    No activity has been recorded for this staff user.

                </p>

            </div>

            @endif

        </div>

    </div>

</div>


<style>
    .staff-avatar {

        width: 52px;
        height: 52px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 14px;

        background:
            linear-gradient(135deg,
                #8063ff,
                #6040ed);

        color: #fff;

        font-family: 'Outfit', sans-serif;
        font-size: 1.2rem;
        font-weight: 700;

        box-shadow:
            0 5px 12px rgba(96, 64, 237, .18);

    }


    .activity-table th {

        font-size: .78rem;

        text-transform: uppercase;

        letter-spacing: .03em;

        color: #6c757d;

        white-space: nowrap;

    }


    .activity-icon {

        width: 34px;
        height: 34px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 9px;

        background: #f0edff;

        color: #6040ed;

    }


    .activity-details {

        min-width: 300px;
        max-width: 500px;

    }


    .activity-changes {

        padding: 8px 10px;

        border-radius: 8px;

        background: #f8f9fa;

    }


    .empty-state-icon {

        width: 64px;
        height: 64px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-left: auto;
        margin-right: auto;

        border-radius: 16px;

        background: #eeeaff;

        color: var(--app-primary);

        font-size: 1.5rem;

    }


    @media (max-width: 767.98px) {

        .activity-table {
            min-width: 1000px;
        }

    }
</style>

@endsection