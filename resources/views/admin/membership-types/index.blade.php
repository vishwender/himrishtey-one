@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h5 class="mb-1">
                        Membership Types
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
                    data-bs-target="#addMembershipTypeModal">

                    <i class="bi bi-plus-lg me-1"></i>

                    Add Membership Type

                </button>

            </div>


            {{-- Messages --}}

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


            @if(session('error'))

            <div class="alert alert-danger alert-dismissible fade show">

                <i class="bi bi-exclamation-triangle me-2"></i>

                {{ session('error') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

            @endif


            @if($errors->any())

            <div class="alert alert-danger alert-dismissible fade show">

                <ul class="mb-0">

                    @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

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
                        action="{{ route('admin.membership-types.index') }}">

                        <div class="input-group">

                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                class="form-control"
                                placeholder="Search membership types...">

                            <button
                                type="submit"
                                class="btn btn-outline-secondary">

                                <i class="bi bi-search"></i>

                            </button>


                            @if($search)

                            <a
                                href="{{ route('admin.membership-types.index') }}"
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

                            <th width="70">
                                ID
                            </th>

                            <th>
                                Membership Type
                            </th>

                            <th>
                                Guide
                            </th>

                            <th>
                                Description
                            </th>

                            <th>
                                Plans
                            </th>

                            <th width="150">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($membershipTypes as $type)

                        <tr>

                            <td>
                                {{ $type->id }}
                            </td>


                            <td>

                                <strong>
                                    {{ $type->plan_name }}
                                </strong>

                            </td>


                            <td>

                                <span
                                    class="text-muted"
                                    style="
                                            display:block;
                                            max-width:300px;
                                            white-space:nowrap;
                                            overflow:hidden;
                                            text-overflow:ellipsis;
                                        ">

                                    {{ $type->plan_guide }}

                                </span>

                            </td>


                            <td>

                                <span
                                    class="text-muted"
                                    style="
                                            display:block;
                                            max-width:350px;
                                            white-space:nowrap;
                                            overflow:hidden;
                                            text-overflow:ellipsis;
                                        ">

                                    {{ $type->plan_description }}

                                </span>

                            </td>


                            <td>

                                <span class="badge bg-primary">

                                    {{ $type->plans_count }}

                                </span>

                            </td>


                            <td>

                                <div class="d-flex gap-2">


                                    {{-- View --}}

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewType{{ $type->id }}"
                                        title="View">

                                        <i class="bi bi-eye"></i>

                                    </button>


                                    {{-- Edit --}}

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editType{{ $type->id }}"
                                        title="Edit">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>


                                    {{-- Delete --}}

                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.membership-types.destroy',
                                                $type->id
                                            ) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this membership type?');">

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
                            id="viewType{{ $type->id }}"
                            tabindex="-1">

                            <div class="modal-dialog modal-lg modal-dialog-centered">

                                <div class="modal-content">

                                    <div class="modal-header">

                                        <h5 class="modal-title">

                                            {{ $type->plan_name }}

                                        </h5>

                                        <button
                                            type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal">
                                        </button>

                                    </div>


                                    <div class="modal-body">

                                        <div class="mb-4">

                                            <label class="fw-semibold">
                                                Plan Guide
                                            </label>

                                            <div class="border rounded p-3 mt-2 bg-light">

                                                {!! nl2br(e($type->plan_guide)) !!}

                                            </div>

                                        </div>


                                        <div class="mb-4">

                                            <label class="fw-semibold">
                                                Plan Description
                                            </label>

                                            <div class="border rounded p-3 mt-2 bg-light">

                                                {!! nl2br(e($type->plan_description)) !!}

                                            </div>

                                        </div>


                                        <div>

                                            <label class="fw-semibold">
                                                Terms & Conditions
                                            </label>

                                            <div class="border rounded p-3 mt-2 bg-light">

                                                {!! nl2br(e($type->terms_and_conditions)) !!}

                                            </div>

                                        </div>

                                    </div>


                                    <div class="modal-footer">

                                        <button
                                            type="button"
                                            class="btn btn-light"
                                            data-bs-dismiss="modal">

                                            Close

                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- EDIT MODAL --}}

                        <div
                            class="modal fade"
                            id="editType{{ $type->id }}"
                            tabindex="-1">

                            <div class="modal-dialog modal-lg modal-dialog-centered">

                                <div class="modal-content">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.membership-types.update',
                                                $type->id
                                            ) }}">

                                        @csrf

                                        @method('PUT')


                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Edit Membership Type
                                            </h5>

                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                            </button>

                                        </div>


                                        <div class="modal-body">

                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Membership Type
                                                </label>

                                                <input
                                                    type="text"
                                                    name="plan_name"
                                                    value="{{ $type->plan_name }}"
                                                    class="form-control"
                                                    required>

                                            </div>


                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Plan Guide
                                                </label>

                                                <textarea
                                                    name="plan_guide"
                                                    class="form-control"
                                                    rows="4"
                                                    required>{{ $type->plan_guide }}</textarea>

                                            </div>


                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Plan Description
                                                </label>

                                                <textarea
                                                    name="plan_description"
                                                    class="form-control"
                                                    rows="6"
                                                    required>{{ $type->plan_description }}</textarea>

                                            </div>


                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Terms & Conditions
                                                </label>

                                                <textarea
                                                    name="terms_and_conditions"
                                                    class="form-control"
                                                    rows="7"
                                                    required>{{ $type->terms_and_conditions }}</textarea>

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

                                                Update

                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                        @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-5">

                                <i class="bi bi-credit-card fs-1 text-muted"></i>

                                <p class="text-muted mt-2 mb-0">

                                    No membership types found.

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
                    {{ $membershipTypes->firstItem() ?? 0 }}
                    to
                    {{ $membershipTypes->lastItem() ?? 0 }}
                    of
                    {{ $membershipTypes->total() }}
                    entries

                </div>


                <div>

                    {{ $membershipTypes->links() }}

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ADD MODAL --}}

<div
    class="modal fade"
    id="addMembershipTypeModal"
    tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('admin.membership-types.store') }}">

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title">
                        Add Membership Type
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Membership Type
                        </label>

                        <input
                            type="text"
                            name="plan_name"
                            class="form-control"
                            placeholder="e.g. Premium Membership"
                            required>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Plan Guide
                        </label>

                        <textarea
                            name="plan_guide"
                            class="form-control"
                            rows="4"
                            placeholder="Enter features included in this membership..."
                            required></textarea>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Plan Description
                        </label>

                        <textarea
                            name="plan_description"
                            class="form-control"
                            rows="6"
                            placeholder="Describe this membership..."
                            required></textarea>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Terms & Conditions
                        </label>

                        <textarea
                            name="terms_and_conditions"
                            class="form-control"
                            rows="7"
                            placeholder="Enter terms and conditions..."
                            required></textarea>

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

                        Add Membership Type

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection