@extends('admin.layout')

@section('title', 'Profile Ranges')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Profile Ranges
            </h4>

            <p class="text-muted mb-0">
                Manage profile ranges and their corresponding rates.
            </p>
        </div>

        <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addProfileRangeModal">

            <i class="bi bi-plus-lg me-1"></i>
            Add Profile Range

        </button>

    </div>


    {{-- Flash Messages --}}

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


    {{-- Validation Errors --}}

    @if($errors->any())

    <div class="alert alert-danger">

        <ul class="mb-0">

            @foreach($errors->all() as $error)

            <li>
                {{ $error }}
            </li>

            @endforeach

        </ul>

    </div>

    @endif


    {{-- Table --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <h6 class="fw-bold mb-0">
                    Profile Ranges
                </h6>

                <span class="text-muted small">
                    {{ $ranges->total() }} range(s)
                </span>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th style="width:80px;">
                            #
                        </th>

                        <th>
                            Range From
                        </th>

                        <th>
                            Range To
                        </th>

                        <th>
                            Rate
                        </th>

                        <th class="text-end">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($ranges as $range)

                    <tr>

                        <td>
                            {{ $range->id }}
                        </td>


                        <td>

                            <span class="fw-semibold">
                                {{ $range->range_from }}
                            </span>

                        </td>


                        <td>

                            <span class="fw-semibold">
                                {{ $range->range_to }}
                            </span>

                        </td>


                        <td>

                            <span class="fw-semibold">

                                ₹{{ number_format(
                                        (float) $range->rate,
                                        2
                                    ) }}

                            </span>

                        </td>


                        <td class="text-end">

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary edit-range-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#editProfileRangeModal"
                                data-id="{{ $range->id }}"
                                data-range-from="{{ $range->range_from }}"
                                data-range-to="{{ $range->range_to }}"
                                data-rate="{{ $range->rate }}">

                                <i class="bi bi-pencil me-1"></i>
                                Edit

                            </button>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center py-5">

                            <div class="text-muted">

                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                No profile ranges found.

                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($ranges->hasPages())

        <div class="card-footer bg-white">

            {{ $ranges->links() }}

        </div>

        @endif

    </div>

</div>


{{-- ================================================================
    ADD PROFILE RANGE MODAL
================================================================= --}}

<div
    class="modal fade"
    id="addProfileRangeModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form
                action="{{ route('admin.profile-ranges.store') }}"
                method="POST">

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title fw-bold">
                        Add Profile Range
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

                            <label class="form-label fw-semibold">
                                Range From
                            </label>

                            <input
                                type="number"
                                name="range_from"
                                class="form-control"
                                min="0"
                                value="{{ old('range_from') }}"
                                required>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Range To
                            </label>

                            <input
                                type="number"
                                name="range_to"
                                class="form-control"
                                min="0"
                                value="{{ old('range_to') }}"
                                required>

                        </div>


                        <div class="col-12">

                            <label class="form-label fw-semibold">
                                Rate
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    ₹
                                </span>

                                <input
                                    type="number"
                                    name="rate"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('rate') }}"
                                    required>

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

                        <i class="bi bi-check-lg me-1"></i>
                        Save Range

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- ================================================================
    EDIT PROFILE RANGE MODAL
================================================================= --}}

<div
    class="modal fade"
    id="editProfileRangeModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form
                id="editProfileRangeForm"
                method="POST">

                @csrf
                @method('PUT')


                <div class="modal-header">

                    <h5 class="modal-title fw-bold">
                        Edit Profile Range
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

                            <label class="form-label fw-semibold">
                                Range From
                            </label>

                            <input
                                type="number"
                                id="editRangeFrom"
                                name="range_from"
                                class="form-control"
                                min="0"
                                required>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Range To
                            </label>

                            <input
                                type="number"
                                id="editRangeTo"
                                name="range_to"
                                class="form-control"
                                min="0"
                                required>

                        </div>


                        <div class="col-12">

                            <label class="form-label fw-semibold">
                                Rate
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    ₹
                                </span>

                                <input
                                    type="number"
                                    id="editRate"
                                    name="rate"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    required>

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

                        <i class="bi bi-check-lg me-1"></i>
                        Update Range

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const editButtons =
            document.querySelectorAll('.edit-range-btn');

        const editForm =
            document.getElementById('editProfileRangeForm');

        const rangeFrom =
            document.getElementById('editRangeFrom');

        const rangeTo =
            document.getElementById('editRangeTo');

        const rate =
            document.getElementById('editRate');


        editButtons.forEach(function(button) {

            button.addEventListener('click', function() {

                const id =
                    this.dataset.id;

                rangeFrom.value =
                    this.dataset.rangeFrom;

                rangeTo.value =
                    this.dataset.rangeTo;

                rate.value =
                    this.dataset.rate;


                editForm.action =
                    "{{ url('/admin/profile-ranges') }}/" + id;

            });

        });

    });
</script>

@endsection