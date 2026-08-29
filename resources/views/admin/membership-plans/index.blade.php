@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h5 class="mb-1">
                        Membership Plans
                    </h5>

                    <div
                        style="
                            width:60px;
                            height:3px;
                            background:#ff5a43;
                            border-radius:5px;
                        ">
                    </div>

                </div>

                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addMembershipPlanModal">

                    <i class="bi bi-plus-lg me-1"></i>

                    Add Membership Plan

                </button>

            </div>


            {{-- Success --}}
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


            {{-- Errors --}}
            @if($errors->any())

            <div class="alert alert-danger alert-dismissible fade show">

                <ul class="mb-0">

                    @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                    @endforeach

                </ul>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

            @endif


            {{-- Search --}}
            <div class="row mb-3">

                <div class="col-md-5 ms-auto">

                    <form
                        method="GET"
                        action="{{ route('admin.membership-plans.index') }}">

                        <div class="input-group">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                value="{{ $search }}"
                                placeholder="Search membership plans...">

                            <button
                                type="submit"
                                class="btn btn-outline-secondary">

                                <i class="bi bi-search"></i>

                            </button>

                            @if($search)

                            <a
                                href="{{ route('admin.membership-plans.index') }}"
                                class="btn btn-outline-secondary">

                                <i class="bi bi-x-lg"></i>

                            </a>

                            @endif

                        </div>

                    </form>

                </div>

            </div>


            {{-- Table --}}
            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>ID</th>

                            <th>Membership Type</th>

                            <th>Plan</th>

                            <th>Duration</th>

                            <th>Contact Views</th>

                            <th>Profile Views</th>

                            <th>Cost</th>

                            <th>Discount</th>

                            <th>Final Cost</th>

                            <th style="width:150px;">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($plans as $plan)

                        <tr>

                            <td>
                                {{ $plan->id }}
                            </td>


                            <td>

                                @if($plan->membershipType)

                                <span class="badge bg-light text-dark border">

                                    {{ $plan->membershipType->plan_name }}

                                </span>

                                @else

                                <span class="text-muted">
                                    -
                                </span>

                                @endif

                            </td>


                            <td>

                                <strong>
                                    {{ $plan->plan_name }}
                                </strong>

                            </td>


                            <td>
                                {{ $plan->duration_days }} days
                            </td>


                            <td>
                                {{ number_format($plan->view_contact) }}
                            </td>


                            <td>
                                {{ number_format($plan->view_profile) }}
                            </td>


                            <td>
                                ₹{{ number_format($plan->plan_cost) }}
                            </td>


                            <td>

                                @if($plan->discount_percentage > 0)

                                <span class="badge bg-success">

                                    {{ $plan->discount_percentage }}%

                                </span>

                                @else

                                0%

                                @endif

                            </td>


                            <td>

                                <strong>
                                    ₹{{ number_format((int) $plan->final_cost) }}
                                </strong>

                            </td>


                            <td>

                                <div class="d-flex gap-2">

                                    {{-- View --}}
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewPlan{{ $plan->id }}"
                                        title="View">

                                        <i class="bi bi-eye"></i>

                                    </button>


                                    {{-- Edit --}}
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editPlan{{ $plan->id }}"
                                        title="Edit">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>


                                    {{-- Delete --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.membership-plans.destroy',
                                                $plan->id
                                            ) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this membership plan?');">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>


                        {{-- VIEW MODAL --}}
                        <div
                            class="modal fade"
                            id="viewPlan{{ $plan->id }}"
                            tabindex="-1">

                            <div class="modal-dialog modal-dialog-centered">

                                <div class="modal-content">

                                    <div class="modal-header">

                                        <h5 class="modal-title">
                                            {{ $plan->plan_name }}
                                        </h5>

                                        <button
                                            type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal">
                                        </button>

                                    </div>


                                    <div class="modal-body">

                                        <div class="row g-3">

                                            <div class="col-md-6">

                                                <small class="text-muted">
                                                    Membership Type
                                                </small>

                                                <div class="fw-semibold">

                                                    {{ $plan->membershipType->plan_name ?? '-' }}

                                                </div>

                                            </div>


                                            <div class="col-md-6">

                                                <small class="text-muted">
                                                    Duration
                                                </small>

                                                <div class="fw-semibold">

                                                    {{ $plan->duration_days }} days

                                                </div>

                                            </div>


                                            <div class="col-md-6">

                                                <small class="text-muted">
                                                    Contact Views
                                                </small>

                                                <div class="fw-semibold">

                                                    {{ number_format($plan->view_contact) }}

                                                </div>

                                            </div>


                                            <div class="col-md-6">

                                                <small class="text-muted">
                                                    Profile Views
                                                </small>

                                                <div class="fw-semibold">

                                                    {{ number_format($plan->view_profile) }}

                                                </div>

                                            </div>


                                            <div class="col-md-4">

                                                <small class="text-muted">
                                                    Plan Cost
                                                </small>

                                                <div class="fw-semibold">

                                                    ₹{{ number_format($plan->plan_cost) }}

                                                </div>

                                            </div>


                                            <div class="col-md-4">

                                                <small class="text-muted">
                                                    Discount
                                                </small>

                                                <div class="fw-semibold">

                                                    {{ $plan->discount_percentage }}%

                                                </div>

                                            </div>


                                            <div class="col-md-4">

                                                <small class="text-muted">
                                                    Final Cost
                                                </small>

                                                <div class="fw-bold">

                                                    ₹{{ number_format((int) $plan->final_cost) }}

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- EDIT MODAL --}}
                        <div
                            class="modal fade"
                            id="editPlan{{ $plan->id }}"
                            tabindex="-1">

                            <div class="modal-dialog modal-lg modal-dialog-centered">

                                <div class="modal-content">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.membership-plans.update',
                                                $plan->id
                                            ) }}">

                                        @csrf
                                        @method('PUT')


                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Edit Membership Plan
                                            </h5>

                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                            </button>

                                        </div>


                                        <div class="modal-body">

                                            <div class="row g-3">

                                                {{-- Membership Type --}}
                                                <div class="col-md-6">

                                                    <label class="form-label">
                                                        Membership Type
                                                    </label>

                                                    <select
                                                        name="membership_type"
                                                        class="form-select"
                                                        required>

                                                        <option value="">
                                                            Select Membership Type
                                                        </option>

                                                        @foreach($membershipTypes as $type)

                                                        <option
                                                            value="{{ $type->id }}"
                                                            {{ (string) $plan->membership_type === (string) $type->id ? 'selected' : '' }}>

                                                            {{ $type->plan_name }}

                                                        </option>

                                                        @endforeach

                                                    </select>

                                                </div>


                                                {{-- Plan Name --}}
                                                <div class="col-md-6">

                                                    <label class="form-label">
                                                        Plan Name
                                                    </label>

                                                    <input
                                                        type="text"
                                                        name="plan_name"
                                                        class="form-control"
                                                        value="{{ $plan->plan_name }}"
                                                        required>

                                                </div>


                                                {{-- Duration --}}
                                                <div class="col-md-4">

                                                    <label class="form-label">
                                                        Duration (Days)
                                                    </label>

                                                    <input
                                                        type="number"
                                                        name="duration_days"
                                                        class="form-control"
                                                        value="{{ $plan->duration_days }}"
                                                        min="1"
                                                        required>

                                                </div>


                                                {{-- Contact Views --}}
                                                <div class="col-md-4">

                                                    <label class="form-label">
                                                        Contact Views
                                                    </label>

                                                    <input
                                                        type="number"
                                                        name="view_contact"
                                                        class="form-control"
                                                        value="{{ $plan->view_contact }}"
                                                        min="0"
                                                        required>

                                                </div>


                                                {{-- Profile Views --}}
                                                <div class="col-md-4">

                                                    <label class="form-label">
                                                        Profile Views
                                                    </label>

                                                    <input
                                                        type="number"
                                                        name="view_profile"
                                                        class="form-control"
                                                        value="{{ $plan->view_profile }}"
                                                        min="0"
                                                        required>

                                                </div>


                                                {{-- Plan Cost --}}
                                                <div class="col-md-4">

                                                    <label class="form-label">
                                                        Plan Cost
                                                    </label>

                                                    <div class="input-group">

                                                        <span class="input-group-text">
                                                            ₹
                                                        </span>

                                                        <input
                                                            type="number"
                                                            name="plan_cost"
                                                            class="form-control plan-cost"
                                                            value="{{ $plan->plan_cost }}"
                                                            min="0"
                                                            required>

                                                    </div>

                                                </div>


                                                {{-- Discount --}}
                                                <div class="col-md-4">

                                                    <label class="form-label">
                                                        Discount (%)
                                                    </label>

                                                    <div class="input-group">

                                                        <input
                                                            type="number"
                                                            name="discount_percentage"
                                                            class="form-control discount-percent"
                                                            value="{{ $plan->discount_percentage }}"
                                                            min="0"
                                                            max="100"
                                                            required>

                                                        <span class="input-group-text">
                                                            %
                                                        </span>

                                                    </div>

                                                </div>


                                                {{-- Final Cost --}}
                                                <div class="col-md-4">

                                                    <label class="form-label">
                                                        Final Cost
                                                    </label>

                                                    <div class="input-group">

                                                        <span class="input-group-text">
                                                            ₹
                                                        </span>

                                                        <input
                                                            type="text"
                                                            class="form-control final-cost"
                                                            value="{{ $plan->final_cost }}"
                                                            readonly>

                                                    </div>

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
                                                type="submit"
                                                class="btn btn-primary">

                                                <i class="bi bi-check-circle me-1"></i>

                                                Update Plan

                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                        @empty

                        <tr>

                            <td
                                colspan="10"
                                class="text-center py-5">

                                <i class="bi bi-credit-card fs-1 text-muted"></i>

                                <p class="text-muted mt-2 mb-0">

                                    No membership plans found.

                                </p>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">

                <div class="text-muted small">

                    Showing
                    {{ $plans->firstItem() ?? 0 }}
                    to
                    {{ $plans->lastItem() ?? 0 }}
                    of
                    {{ $plans->total() }}
                    entries

                </div>

                <div>

                    {{ $plans->links() }}

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ADD MODAL --}}
<div
    class="modal fade"
    id="addMembershipPlanModal"
    tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('admin.membership-plans.store') }}">

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title">
                        Add Membership Plan
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <div class="row g-3">

                        {{-- Membership Type --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Membership Type
                            </label>

                            <select
                                name="membership_type"
                                class="form-select"
                                required>

                                <option value="">
                                    Select Membership Type
                                </option>

                                @foreach($membershipTypes as $type)

                                <option value="{{ $type->id }}">

                                    {{ $type->plan_name }}

                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Plan Name --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Plan Name
                            </label>

                            <input
                                type="text"
                                name="plan_name"
                                class="form-control"
                                placeholder="e.g. GOLD"
                                required>

                        </div>


                        {{-- Duration --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                Duration (Days)
                            </label>

                            <input
                                type="number"
                                name="duration_days"
                                class="form-control"
                                min="1"
                                placeholder="365"
                                required>

                        </div>


                        {{-- Contact Views --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                Contact Views
                            </label>

                            <input
                                type="number"
                                name="view_contact"
                                class="form-control"
                                min="0"
                                placeholder="40"
                                required>

                        </div>


                        {{-- Profile Views --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                Profile Views
                            </label>

                            <input
                                type="number"
                                name="view_profile"
                                class="form-control"
                                min="0"
                                placeholder="10000"
                                required>

                        </div>


                        {{-- Plan Cost --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                Plan Cost
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    ₹
                                </span>

                                <input
                                    type="number"
                                    name="plan_cost"
                                    class="form-control plan-cost"
                                    min="0"
                                    placeholder="3100"
                                    required>

                            </div>

                        </div>


                        {{-- Discount --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                Discount (%)
                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    name="discount_percentage"
                                    class="form-control discount-percent"
                                    min="0"
                                    max="100"
                                    value="0"
                                    required>

                                <span class="input-group-text">
                                    %
                                </span>

                            </div>

                        </div>


                        {{-- Final Cost --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                Final Cost
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    ₹
                                </span>

                                <input
                                    type="text"
                                    class="form-control final-cost"
                                    value="0"
                                    readonly>

                            </div>

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
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-plus-circle me-1"></i>

                        Add Plan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- FINAL COST CALCULATOR --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        function calculateFinalCost(container) {

            const costInput = container.querySelector('.plan-cost');
            const discountInput = container.querySelector('.discount-percent');
            const finalCostInput = container.querySelector('.final-cost');

            if (!costInput || !discountInput || !finalCostInput) {
                return;
            }

            const cost = parseFloat(costInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;

            const finalCost =
                Math.round(
                    cost - (cost * discount / 100)
                );

            finalCostInput.value = finalCost;
        }


        document.querySelectorAll('.modal').forEach(function(modal) {

            const costInput =
                modal.querySelector('.plan-cost');

            const discountInput =
                modal.querySelector('.discount-percent');


            if (costInput) {

                costInput.addEventListener('input', function() {
                    calculateFinalCost(modal);
                });

            }


            if (discountInput) {

                discountInput.addEventListener('input', function() {
                    calculateFinalCost(modal);
                });

            }


            calculateFinalCost(modal);

        });

    });
</script>

@endsection