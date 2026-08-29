@extends('admin.layout')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/dashboard.css') }}">
@endpush

@section('content')

@php
$currentSite = app(\App\Services\SiteManager::class)->current();

$rotationTodayCount = $rotationTodayCount ?? 0;
$rotationTomorrowCount = $rotationTomorrowCount ?? 0;
$rotationDayAfterTomorrowCount = $rotationDayAfterTomorrowCount ?? 0;

$rotationNotifications = $rotationNotifications ?? collect();
$dashboardRotationAdmins = $dashboardRotationAdmins ?? collect();
@endphp


<div class="container-fluid">

    {{-- ================================================================
        PAGE HEADER
    ================================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="h3 mb-1">
                Dashboard
            </h1>

            <p class="text-muted mb-0">
                Overview of the selected matrimonial site.
            </p>

        </div>


        <div>

            <a
                href="{{ route('admin.site.select') }}"
                class="btn btn-outline-primary">

                Switch Site

            </a>

        </div>

    </div>


    {{-- ================================================================
        CURRENT SITE
    ================================================================= --}}

    @if($currentSite)

    <div class="card border-0 shadow-sm mb-4 dashboard-site-card">

        <div class="card-body">

            <div class="row align-items-center">


                {{-- Site Name --}}

                <div class="col-md-4">

                    <small class="text-muted d-block">
                        Current Site
                    </small>

                    <h5 class="mb-0">
                        {{ $currentSite->name }}
                    </h5>

                </div>


                {{-- Site Code --}}

                <div class="col-md-4">

                    <small class="text-muted d-block">
                        Site Code
                    </small>

                    <h6 class="mb-0">
                        {{ $currentSite->code }}
                    </h6>

                </div>


                {{-- Database --}}

                <div class="col-md-4">

                    <small class="text-muted d-block">
                        Database
                    </small>

                    <h6 class="mb-0">
                        {{ $currentSite->database_name }}
                    </h6>

                </div>


            </div>

        </div>

    </div>

    @else

    <div class="alert alert-warning">

        <strong>
            No site selected.
        </strong>

        <a
            href="{{ route('admin.site.select') }}"
            class="alert-link">

            Select a site

        </a>

    </div>

    @endif


    {{-- ================================================================
        DASHBOARD STATISTICS
    ================================================================= --}}

    <div class="row g-4 mb-4">


        {{-- Members --}}

        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100 dashboard-stat">

                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between mb-3">

                        <div class="text-muted">
                            Members
                        </div>

                        <span class="stat-icon members">

                            <i class="bi bi-people"></i>

                        </span>

                    </div>

                    <h3 class="mb-0">

                        {{ number_format($stats['members'] ?? 0) }}

                    </h3>

                </div>

            </div>

        </div>


        {{-- Active Members --}}

        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100 dashboard-stat">

                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between mb-3">

                        <div class="text-muted">
                            Active Members
                        </div>

                        <span class="stat-icon active">

                            <i class="bi bi-person-check"></i>

                        </span>

                    </div>

                    <h3 class="mb-0">

                        {{ number_format($stats['active_members'] ?? 0) }}

                    </h3>

                </div>

            </div>

        </div>


        {{-- Inactive Profiles --}}

        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100 dashboard-stat">

                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between mb-3">

                        <div class="text-muted">
                            Inactive Profiles
                        </div>

                        <span class="stat-icon inactive">

                            <i class="bi bi-person-dash"></i>

                        </span>

                    </div>

                    <h3 class="mb-0">

                        {{ number_format($stats['inactive_members'] ?? 0) }}

                    </h3>

                </div>

            </div>

        </div>


        {{-- Payments --}}

        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100 dashboard-stat">

                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between mb-3">

                        <div class="text-muted">
                            Payments
                        </div>

                        <span class="stat-icon payments">

                            <i class="bi bi-credit-card"></i>

                        </span>

                    </div>

                    <h3 class="mb-0">

                        {{ number_format($stats['payments'] ?? 0) }}

                    </h3>

                </div>

            </div>

        </div>


    </div>


    {{-- ================================================================
        ROTATION NOTIFICATIONS
    ================================================================= --}}

    {{-- ================================================================
    ROTATION NOTIFICATIONS
================================================================= --}}

    @if(
    $rotationTodayCount > 0 ||
    $rotationTomorrowCount > 0 ||
    $rotationDayAfterTomorrowCount > 0
    )

    <div class="card border-0 shadow-sm mb-4">

        {{-- Header --}}
        <div class="card-header bg-white py-3">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <div>

                    <h5 class="fw-bold mb-1">

                        <i class="bi bi-bell me-2 text-warning"></i>

                        Rotation Notifications

                    </h5>

                    <div class="small text-muted">
                        Upcoming member rotations requiring attention.
                    </div>

                </div>


                <a
                    href="{{ route('admin.rotations.index') }}"
                    class="btn btn-sm btn-outline-primary">

                    <i class="bi bi-arrow-right me-1"></i>

                    View All Rotations

                </a>

            </div>

        </div>


        {{-- Notification Counts --}}
        <div class="card-body">

            <div class="row g-3">


                {{-- Today --}}
                <div class="col-md-4">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="d-flex align-items-center justify-content-between">

                            <div>

                                <div class="text-muted small mb-1">
                                    Today
                                </div>

                                <div class="fs-3 fw-bold text-danger">

                                    {{ $rotationTodayCount }}

                                </div>

                            </div>


                            <div
                                class="d-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger"
                                style="width:48px;height:48px;">

                                <i class="bi bi-exclamation-circle fs-5"></i>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Tomorrow --}}
                <div class="col-md-4">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="d-flex align-items-center justify-content-between">

                            <div>

                                <div class="text-muted small mb-1">
                                    Tomorrow
                                </div>

                                <div class="fs-3 fw-bold text-warning">

                                    {{ $rotationTomorrowCount }}

                                </div>

                            </div>


                            <div
                                class="d-flex align-items-center justify-content-center rounded-circle bg-warning-subtle text-warning"
                                style="width:48px;height:48px;">

                                <i class="bi bi-clock-history fs-5"></i>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Day After Tomorrow --}}
                <div class="col-md-4">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="d-flex align-items-center justify-content-between">

                            <div>

                                <div class="text-muted small mb-1">
                                    Upcoming
                                </div>

                                <div class="fs-3 fw-bold text-primary">

                                    {{ $rotationDayAfterTomorrowCount }}

                                </div>

                            </div>


                            <div
                                class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary"
                                style="width:48px;height:48px;">

                                <i class="bi bi-calendar-event fs-5"></i>

                            </div>

                        </div>

                    </div>

                </div>


            </div>

        </div>

    </div>

    @endif


</div>

@endsection