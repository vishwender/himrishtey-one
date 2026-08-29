@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    {{-- =====================================================
        HEADER
    ====================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h5 class="mb-1">
                        Educations
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
                    data-bs-target="#addEducationModal">

                    <i class="bi bi-plus-lg me-1"></i>
                    Add Education

                </button>

            </div>


            {{-- =================================================
                SUCCESS MESSAGE
            ================================================== --}}

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


            {{-- =================================================
                SEARCH
            ================================================== --}}

            <div class="row mb-3">

                <div class="col-md-4 ms-auto">

                    <form
                        method="GET"
                        action="{{ route('admin.educations.index') }}">

                        <div class="input-group">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search education..."
                                value="{{ $search }}">

                            <button
                                class="btn btn-outline-secondary"
                                type="submit">

                                <i class="bi bi-search"></i>

                            </button>

                        </div>

                    </form>

                </div>

            </div>


            {{-- =================================================
                TABLE
            ================================================== --}}

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th style="width:150px;">
                                ID
                            </th>

                            <th>
                                Education
                            </th>

                            <th style="width:150px;">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($educations as $education)

                        <tr>

                            <td>
                                {{ $education->id }}
                            </td>

                            <td>
                                {{ $education->education }}
                            </td>

                            <td>

                                <div class="d-flex gap-2">

                                    {{-- Edit --}}

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editEducationModal{{ $education->id }}">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>


                                    {{-- Delete --}}

                                    <form
                                        action="{{ route('admin.educations.destroy', $education->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this education?');">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger">

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
                            id="editEducationModal{{ $education->id }}"
                            tabindex="-1"
                            aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered">

                                <div class="modal-content">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.educations.update',
                                                $education->id
                                            ) }}">

                                        @csrf
                                        @method('PUT')


                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Edit Education
                                            </h5>

                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                            </button>

                                        </div>


                                        <div class="modal-body">

                                            <label
                                                class="form-label">

                                                Education

                                            </label>

                                            <input
                                                type="text"
                                                name="education"
                                                class="form-control"
                                                value="{{ $education->education }}"
                                                required
                                                maxlength="255">

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
                                colspan="3"
                                class="text-center py-5">

                                <i
                                    class="bi bi-mortarboard fs-1 text-muted">
                                </i>

                                <p class="text-muted mb-0 mt-2">
                                    No education records found.
                                </p>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- =================================================
                PAGINATION
            ================================================== --}}

            <div class="d-flex justify-content-between align-items-center mt-3">

                <div class="text-muted small">

                    Showing
                    {{ $educations->firstItem() ?? 0 }}
                    to
                    {{ $educations->lastItem() ?? 0 }}
                    of
                    {{ $educations->total() }}
                    entries

                </div>


                <div>

                    {{ $educations->links() }}

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    ADD EDUCATION MODAL
========================================================= --}}

<div
    class="modal fade"
    id="addEducationModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('admin.educations.store') }}">

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title">
                        Add Education
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <label
                        for="education"
                        class="form-label">

                        Education

                    </label>

                    <input
                        type="text"
                        name="education"
                        id="education"
                        class="form-control"
                        placeholder="Enter education"
                        required
                        maxlength="255">

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
                        Add Education

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection