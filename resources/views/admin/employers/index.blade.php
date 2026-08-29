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
                        Employers
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
                    data-bs-target="#addemployerModal">

                    <i class="bi bi-plus-lg me-1"></i>
                    Add Employer

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
                        action="{{ route('admin.employers.index') }}">

                        <div class="input-group">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search employer..."
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
                                Employer
                            </th>

                            <th style="width:150px;">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($employers as $employer)

                        <tr>

                            <td>
                                {{ $employer->id }}
                            </td>

                            <td>
                                {{ $employer->employer }}
                            </td>

                            <td>

                                <div class="d-flex gap-2">

                                    {{-- Edit --}}

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editemployerModal{{ $employer->id }}">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>


                                    {{-- Delete --}}

                                    <form
                                        action="{{ route('admin.employers.destroy', $employer->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this employer?');">

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
                            id="editemployerModal{{ $employer->id }}"
                            tabindex="-1"
                            aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered">

                                <div class="modal-content">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.employers.update',
                                                $employer->id
                                            ) }}">

                                        @csrf
                                        @method('PUT')


                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Edit employer
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

                                                employer

                                            </label>

                                            <input
                                                type="text"
                                                name="employer"
                                                class="form-control"
                                                value="{{ $employer->employer }}"
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
                                    No employer records found.
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
                    {{ $employers->firstItem() ?? 0 }}
                    to
                    {{ $employers->lastItem() ?? 0 }}
                    of
                    {{ $employers->total() }}
                    entries

                </div>


                <div>

                    {{ $employers->links() }}

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    ADD employer MODAL
========================================================= --}}

<div
    class="modal fade"
    id="addemployerModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('admin.employers.store') }}">

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title">
                        Add employer
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <label
                        for="employer"
                        class="form-label">

                        employer

                    </label>

                    <input
                        type="text"
                        name="employer"
                        id="employer"
                        class="form-control"
                        placeholder="Enter employer"
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
                        Add employer

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection