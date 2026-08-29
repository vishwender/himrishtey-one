@extends('admin.layout')

@section('title', 'Payments')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h1 class="h3 mb-1">
                Payments
            </h1>

            <p class="text-muted mb-0">
                View and manage membership payment transactions.
            </p>

        </div>

    </div>


    {{-- ================================================================
        SUMMARY
    ================================================================= --}}

    <div class="row g-4 mb-4">


        {{-- Total Transactions --}}

        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Total Transactions
                    </div>

                    <div class="d-flex align-items-center justify-content-between">

                        <h3 class="mb-0">
                            {{ number_format($totalPayments) }}
                        </h3>

                        <i class="bi bi-receipt fs-3 text-primary"></i>

                    </div>

                </div>

            </div>

        </div>


        {{-- Total Revenue --}}

        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Total Revenue
                    </div>

                    <div class="d-flex align-items-center justify-content-between">

                        <h3 class="mb-0">
                            ₹{{ number_format($totalAmount, 2) }}
                        </h3>

                        <i class="bi bi-currency-rupee fs-3 text-success"></i>

                    </div>

                </div>

            </div>

        </div>


        {{-- Today --}}

        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Today's Revenue
                    </div>

                    <div class="d-flex align-items-center justify-content-between">

                        <h3 class="mb-0">
                            ₹{{ number_format($todayAmount, 2) }}
                        </h3>

                        <i class="bi bi-calendar-check fs-3 text-warning"></i>

                    </div>

                </div>

            </div>

        </div>


        {{-- This Month --}}

        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        This Month
                    </div>

                    <div class="d-flex align-items-center justify-content-between">

                        <h3 class="mb-0">
                            ₹{{ number_format($thisMonthAmount, 2) }}
                        </h3>

                        <i class="bi bi-graph-up-arrow fs-3 text-info"></i>

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
                action="{{ route('admin.payments.index') }}">

                <div class="row g-3 align-items-end">


                    <div class="col-lg-5">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Profile ID, name, mobile or payment ID">

                    </div>


                    <div class="col-md-3 col-lg-2">

                        <label class="form-label">
                            From
                        </label>

                        <input
                            type="date"
                            name="date_from"
                            class="form-control"
                            value="{{ request('date_from') }}">

                    </div>


                    <div class="col-md-3 col-lg-2">

                        <label class="form-label">
                            To
                        </label>

                        <input
                            type="date"
                            name="date_to"
                            class="form-control"
                            value="{{ request('date_to') }}">

                    </div>


                    <div class="col-md-6 col-lg-3">

                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-search me-1"></i>
                            Search

                        </button>

                        <a
                            href="{{ route('admin.payments.index') }}"
                            class="btn btn-light">

                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- ================================================================
        PAYMENT TABLE
    ================================================================= --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Payment History
            </h5>

        </div>


        <div class="table-responsive">

            <table class="table align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Member
                        </th>

                        <th>
                            Profile ID
                        </th>

                        <th>
                            Payment ID
                        </th>

                        <th>
                            Plan
                        </th>

                        <th>
                            Amount
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Remarks
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($payments as $payment)

                    @php
                    $member = $payment->member;
                    @endphp

                    <tr>

                        <td>
                            {{ $payment->id }}
                        </td>


                        <td>

                            @if($member)

                            <div class="fw-semibold">
                                {{ $member->full_name }}
                            </div>

                            @if(!empty($member->mobile_number))

                            <small class="text-muted">
                                {{ $member->mobile_number }}
                            </small>

                            @endif

                            @else

                            <span class="text-danger">
                                Member Removed
                            </span>

                            @endif

                        </td>


                        <td>

                            @if($member)

                            <a
                                href="{{ route(
                                            'admin.members.show',
                                            $member->id
                                        ) }}"
                                class="text-decoration-none fw-semibold">

                                {{ $member->profile_id }}

                            </a>

                            @else

                            <span class="text-muted">
                                ID: {{ $payment->member_id }}
                            </span>

                            @endif

                        </td>


                        <td>

                            <code>
                                {{ $payment->payment_id }}
                            </code>

                        </td>


                        <td>

                            <span class="badge bg-light text-dark border">

                                Plan #{{ $payment->plan_id }}

                            </span>

                        </td>


                        <td>

                            <span class="fw-bold text-success">

                                ₹{{ number_format(
                                        $payment->amount,
                                        2
                                    ) }}

                            </span>

                        </td>


                        <td>

                            @if($payment->payment_date)

                            <div>
                                {{ $payment->payment_date->format('d M Y') }}
                            </div>

                            <small class="text-muted">
                                {{ $payment->payment_date->format('h:i A') }}
                            </small>

                            @else

                            —

                            @endif

                        </td>


                        <td>

                            {{ $payment->remarks ?: '—' }}

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="8"
                            class="text-center py-5">

                            <i class="bi bi-receipt fs-2 text-muted d-block mb-2"></i>

                            <div class="text-muted">
                                No payments found.
                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($payments->hasPages())

        <div class="card-footer bg-white">

            {{ $payments->links() }}

        </div>

        @endif

    </div>

</div>

@endsection