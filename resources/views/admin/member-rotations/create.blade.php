@extends('admin.layout')

@section('title', 'Create Rotation')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
         Header
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">
                Create Rotation
            </h1>

            <p class="text-muted mb-0">
                Schedule a member rotation and assign it to an admin.
            </p>
        </div>

        <div class="d-flex gap-2">

            <a
                href="{{ route('admin.rotations.index') }}"
                class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left me-1"></i>

                Back to Rotations

            </a>

        </div>

    </div>


    {{-- =========================================================
         Validation Errors
    ========================================================== --}}

    @if($errors->any())

    <div class="alert alert-danger">

        <div class="fw-semibold mb-2">
            Please fix the following errors:
        </div>

        <ul class="mb-0">

            @foreach($errors->all() as $error)

            <li>
                {{ $error }}
            </li>

            @endforeach

        </ul>

    </div>

    @endif


    {{-- =========================================================
         Success Message
    ========================================================== --}}

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


    {{-- =========================================================
         Create Rotation Card
    ========================================================== --}}

    <div class="row">

        <div class="col-xl-8 col-lg-10">

            <div class="card border-0 shadow-sm">

                {{-- Card Header --}}

                <div class="card-header bg-white py-3">

                    <h5 class="mb-1">
                        Rotation Details
                    </h5>

                    <small class="text-muted">
                        Select the member, assign an admin and schedule the
                        next rotation.
                    </small>

                </div>


                {{-- Form --}}

                <div class="card-body">

                    <form
                        method="POST"
                        action="">

                        @csrf


                        {{-- =================================================
                             Member
                        ================================================== --}}

                        <div class="mb-4">

                            <label
                                for="member_id"
                                class="form-label fw-semibold">

                                Member

                                <span class="text-danger">
                                    *
                                </span>

                            </label>

                            <select
                                name="member_id"
                                id="member_id"
                                class="form-select @error('member_id') is-invalid @enderror"
                                required>

                                <option value="">
                                    Select Member
                                </option>

                                

                            </select>

                            @error('member_id')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                            @enderror

                            <div class="form-text">
                                Select the member whose rotation needs to be scheduled.
                            </div>

                        </div>


                        {{-- =================================================
                             Assign To
                        ================================================== --}}

                        <div class="mb-4">

                            <label
                                for="user_id"
                                class="form-label fw-semibold">

                                Assign To

                                <span class="text-danger">
                                    *
                                </span>

                            </label>

                            <select
                                name="user_id"
                                id="user_id"
                                class="form-select @error('user_id') is-invalid @enderror"
                                required>

                                <option value="">
                                    Select Admin
                                </option>

                                

                            </select>

                            @error('user_id')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                            @enderror

                            <div class="form-text">
                                The selected admin will be responsible for this rotation.
                            </div>

                        </div>


                        {{-- =================================================
                             Rotation Days
                        ================================================== --}}

                        <div class="mb-4">

                            <label
                                for="days"
                                class="form-label fw-semibold">

                                Rotation Period

                                <span class="text-danger">
                                    *
                                </span>

                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    name="days"
                                    id="days"
                                    min="1"
                                    max="365"
                                    value="{{ old('days', 7) }}"
                                    class="form-control @error('days') is-invalid @enderror"
                                    required>

                                <span class="input-group-text">
                                    Days
                                </span>

                            </div>

                            @error('days')

                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>

                            @enderror

                            <div class="form-text">
                                Number of days before the member rotates again.
                            </div>

                        </div>


                        {{-- =================================================
                             Rotation Date
                        ================================================== --}}

                        <div class="mb-4">

                            <label
                                for="next_rotation_at"
                                class="form-label fw-semibold">

                                Next Rotation

                                <span class="text-danger">
                                    *
                                </span>

                            </label>

                            <input
                                type="datetime-local"
                                name="next_rotation_at"
                                id="next_rotation_at"
                                value="{{ old('next_rotation_at') }}"
                                class="form-control @error('next_rotation_at') is-invalid @enderror"
                                required>

                            @error('next_rotation_at')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                            @enderror

                            <div class="form-text">
                                Select the date and time when this rotation should take place.
                            </div>

                        </div>


                        {{-- =================================================
                             Status
                        ================================================== --}}

                        <div class="mb-4">

                            <label
                                for="status"
                                class="form-label fw-semibold">

                                Status

                            </label>

                            <select
                                name="status"
                                id="status"
                                class="form-select @error('status') is-invalid @enderror">

                                <option
                                    value="pending"
                                    {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>

                                    Pending

                                </option>

                                <option
                                    value="completed"
                                    {{ old('status') === 'completed' ? 'selected' : '' }}>

                                    Completed

                                </option>

                                <option
                                    value="cancelled"
                                    {{ old('status') === 'cancelled' ? 'selected' : '' }}>

                                    Cancelled

                                </option>

                            </select>

                            @error('status')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                            @enderror

                        </div>


                        {{-- =================================================
                             Form Actions
                        ================================================== --}}

                        <div class="border-top pt-4 mt-4">

                            <div class="d-flex justify-content-end gap-2">

                                <a
                                    href="{{ route('admin.rotations.index') }}"
                                    class="btn btn-outline-secondary">

                                    Cancel

                                </a>

                                <button
                                    type="submit"
                                    class="btn btn-primary">

                                    <i class="bi bi-plus-circle me-1"></i>

                                    Create Rotation

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        {{-- =========================================================
             Information Card
        ========================================================== --}}

        <div class="col-xl-4 col-lg-2 mt-4 mt-xl-0">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">

                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        Rotation Information
                    </h6>

                </div>

                <div class="card-body">

                    <div class="mb-3">

                        <div class="fw-semibold mb-1">
                            Member
                        </div>

                        <small class="text-muted">
                            Select the member who needs to be rotated.
                        </small>

                    </div>


                    <div class="mb-3">

                        <div class="fw-semibold mb-1">
                            Assigned Admin
                        </div>

                        <small class="text-muted">
                            The admin assigned to the rotation will handle
                            the member during this period.
                        </small>

                    </div>


                    <div class="mb-3">

                        <div class="fw-semibold mb-1">
                            Rotation Period
                        </div>

                        <small class="text-muted">
                            Specify how many days the member should remain
                            assigned before the next rotation.
                        </small>

                    </div>


                    <div>

                        <div class="fw-semibold mb-1">
                            Next Rotation
                        </div>

                        <small class="text-muted">
                            This is the date and time when the rotation
                            becomes due.
                        </small>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection