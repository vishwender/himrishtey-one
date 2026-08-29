@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- =====================================================
                HEADER
            ====================================================== --}}

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h5 class="mb-1">
                        Occupations
                    </h5>

                    <div
                        class="border-bottom"
                        style="width:60px;">
                    </div>
                </div>


                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addOccupationModal">

                    <i class="bi bi-plus-lg me-1"></i>
                    Add Occupation

                </button>

            </div>


            {{-- =====================================================
                SUCCESS MESSAGE
            ====================================================== --}}

            @if(session('success'))

            <div
                class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

            @endif


            {{-- =====================================================
                VALIDATION ERRORS
            ====================================================== --}}

            @if($errors->any())

            <div
                class="alert alert-danger alert-dismissible fade show">

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


            {{-- =====================================================
                SEARCH
            ====================================================== --}}

            <div class="row mb-3">

                <div class="col-md-4 ms-auto">

                    <form
                        method="GET"
                        action="{{ route('admin.occupations.index') }}">

                        <div class="input-group">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search occupation..."
                                value="{{ $search }}">

                            <button
                                class="btn btn-outline-secondary"
                                type="submit">

                                <i class="bi bi-search"></i>

                            </button>

                            @if($search)

                            <a
                                href="{{ route('admin.occupations.index') }}"
                                class="btn btn-outline-secondary">

                                <i class="bi bi-x-lg"></i>

                            </a>

                            @endif

                        </div>

                    </form>

                </div>

            </div>


            {{-- =====================================================
                TABLE
            ====================================================== --}}

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th style="width:100px;">
                                ID
                            </th>

                            <th>
                                Occupation
                            </th>

                            <th style="width:140px;">
                                Status
                            </th>

                            <th style="width:180px;">
                                Adding Date
                            </th>

                            <th style="width:180px;">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($occupations as $occupation)

                        <tr>

                            {{-- ID --}}
                            <td>
                                {{ $occupation->id }}
                            </td>


                            {{-- Occupation --}}
                            <td>

                                <span class="fw-medium">
                                    {{ $occupation->occupation }}
                                </span>

                            </td>


                            {{-- Status --}}
                            <td>

                                @if($occupation->status == 1)

                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">

                                    <i class="bi bi-check-circle me-1"></i>
                                    Active

                                </span>

                                @else

                                <span
                                    class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">

                                    <i class="bi bi-pause-circle me-1"></i>
                                    Inactive

                                </span>

                                @endif

                            </td>


                            {{-- Adding Date --}}
                            <td>

                                @if(
                                $occupation->adding_date &&
                                $occupation->adding_date->format('Y-m-d H:i:s') !== '0000-00-00 00:00:00'
                                )

                                {{ $occupation->adding_date->format('d M Y, h:i A') }}

                                @else

                                <span class="text-muted">
                                    -
                                </span>

                                @endif

                            </td>


                            {{-- Actions --}}
                            <td>

                                <div class="d-flex gap-2">

                                    {{-- Edit --}}
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editOccupationModal{{ $occupation->id }}"
                                        title="Edit">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>


                                    {{-- Toggle Status --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.occupations.toggle-status',
                                                $occupation->id
                                            ) }}">

                                        @csrf
                                        @method('PATCH')

                                        @if($occupation->status == 1)

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Deactivate"
                                            onclick="return confirm('Are you sure you want to deactivate this occupation?');">

                                            <i class="bi bi-pause-circle"></i>

                                        </button>

                                        @else

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-success"
                                            title="Activate"
                                            onclick="return confirm('Are you sure you want to activate this occupation?');">

                                            <i class="bi bi-play-circle"></i>

                                        </button>

                                        @endif

                                    </form>
                                    {{-- DELETE --}}
                                    <form
                                        method="POST"
                                        action="{{ route('admin.occupations.destroy', $occupation->id) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this occupation?');">

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


                        {{-- =================================================
                                EDIT MODAL
                            ================================================== --}}

                        <div
                            class="modal fade"
                            id="editOccupationModal{{ $occupation->id }}"
                            tabindex="-1"
                            aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered">

                                <div class="modal-content">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.occupations.update',
                                                $occupation->id
                                            ) }}">

                                        @csrf
                                        @method('PUT')


                                        {{-- Modal Header --}}
                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Edit Occupation
                                            </h5>

                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                            </button>

                                        </div>


                                        {{-- Modal Body --}}
                                        <div class="modal-body">

                                            {{-- Occupation --}}
                                            <div class="mb-3">

                                                <label
                                                    class="form-label">

                                                    Occupation

                                                </label>

                                                <input
                                                    type="text"
                                                    name="occupation"
                                                    class="form-control"
                                                    value="{{ $occupation->occupation }}"
                                                    required
                                                    maxlength="255">

                                            </div>


                                            {{-- Status --}}
                                            <div class="mb-3">

                                                <label
                                                    class="form-label">

                                                    Status

                                                </label>

                                                <select
                                                    name="status"
                                                    class="form-select"
                                                    required>

                                                    <option
                                                        value="1"
                                                        {{ $occupation->status == 1 ? 'selected' : '' }}>

                                                        Active

                                                    </option>

                                                    <option
                                                        value="0"
                                                        {{ $occupation->status == 0 ? 'selected' : '' }}>

                                                        Inactive

                                                    </option>

                                                </select>

                                            </div>

                                        </div>


                                        {{-- Modal Footer --}}
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
                                                Update Occupation

                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                        @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center py-5">

                                <i
                                    class="bi bi-briefcase fs-1 text-muted">
                                </i>

                                <p class="text-muted mb-0 mt-2">
                                    No occupations found.
                                </p>

                                @if($search)

                                <a
                                    href="{{ route('admin.occupations.index') }}"
                                    class="btn btn-sm btn-outline-primary mt-3">

                                    Clear Search

                                </a>

                                @endif

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- =====================================================
                PAGINATION
            ====================================================== --}}

            <div class="d-flex justify-content-between align-items-center mt-3">

                <div class="text-muted small">

                    Showing
                    {{ $occupations->firstItem() ?? 0 }}
                    to
                    {{ $occupations->lastItem() ?? 0 }}
                    of
                    {{ $occupations->total() }}
                    entries

                </div>


                <div>

                    {{ $occupations->links() }}

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    ADD OCCUPATION MODAL
========================================================= --}}

<div
    class="modal fade"
    id="addOccupationModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('admin.occupations.store') }}">

                @csrf


                {{-- Header --}}
                <div class="modal-header">

                    <h5 class="modal-title">
                        Add Occupation
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                {{-- Body --}}
                <div class="modal-body">

                    <div class="mb-3">

                        <label
                            for="occupation"
                            class="form-label">

                            Occupation

                        </label>

                        <input
                            type="text"
                            name="occupation"
                            id="occupation"
                            class="form-control"
                            placeholder="Enter occupation"
                            required
                            maxlength="255">

                    </div>


                    <div
                        class="alert alert-info mb-0">

                        <i class="bi bi-info-circle me-1"></i>

                        New occupations will be added as
                        <strong>Active</strong>.

                    </div>

                </div>


                {{-- Footer --}}
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
                        Add Occupation

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection