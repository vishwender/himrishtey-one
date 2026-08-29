@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h5 class="mb-1">
                        Religions
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
                    data-bs-target="#addReligionModal">

                    <i class="bi bi-plus-lg me-1"></i>
                    Add Religion

                </button>

            </div>


            {{-- Success Message --}}
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


            {{-- Error Message --}}
            @if(session('error'))

            <div
                class="alert alert-danger alert-dismissible fade show">

                <i class="bi bi-exclamation-triangle me-2"></i>

                {{ session('error') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

            @endif


            {{-- Validation Errors --}}
            @if($errors->any())

            <div
                class="alert alert-danger alert-dismissible fade show">

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

                <div class="col-md-4 ms-auto">

                    <form
                        method="GET"
                        action="{{ route('admin.religions.index') }}">

                        <div class="input-group">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search religion..."
                                value="{{ $search ?? '' }}">

                            <button
                                class="btn btn-outline-secondary"
                                type="submit"
                                title="Search">

                                <i class="bi bi-search"></i>

                            </button>

                            @if(!empty($search))

                            <a
                                href="{{ route('admin.religions.index') }}"
                                class="btn btn-outline-secondary"
                                title="Clear Search">

                                <i class="bi bi-x-lg"></i>

                            </a>

                            @endif

                        </div>

                    </form>

                </div>

            </div>


            {{-- Table --}}
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th style="width:120px;">
                                ID
                            </th>

                            <th>
                                Religion
                            </th>

                            <th style="width:150px;">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($religions as $religion)

                        <tr>

                            <td>
                                {{ $religion->id }}
                            </td>

                            <td>
                                <span class="fw-medium">
                                    {{ $religion->religion ?: '-' }}
                                </span>
                            </td>

                            <td>

                                <div class="d-flex gap-2">

                                    {{-- Edit --}}
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editReligionModal{{ $religion->id }}"
                                        title="Edit">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>


                                    {{-- Delete --}}
                                    <form
                                        action="{{ route(
                                            'admin.religions.destroy',
                                            $religion->id
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this religion?');">

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


                        {{-- Edit Religion Modal --}}
                        <div
                            class="modal fade"
                            id="editReligionModal{{ $religion->id }}"
                            tabindex="-1"
                            aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered">

                                <div class="modal-content">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.religions.update',
                                            $religion->id
                                        ) }}">

                                        @csrf
                                        @method('PUT')


                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Edit Religion
                                            </h5>

                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                            </button>

                                        </div>


                                        <div class="modal-body">

                                            {{-- Religion --}}
                                            <div class="mb-3">

                                                <label
                                                    for="religion_{{ $religion->id }}"
                                                    class="form-label">

                                                    Religion

                                                </label>

                                                <input
                                                    type="text"
                                                    name="religion"
                                                    id="religion_{{ $religion->id }}"
                                                    class="form-control"
                                                    value="{{ $religion->religion }}"
                                                    required
                                                    maxlength="255">

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
                                colspan="3"
                                class="text-center py-5">

                                <i
                                    class="bi bi-bookmark fs-1 text-muted">
                                </i>

                                <p class="text-muted mb-0 mt-2">
                                    No religions found.
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
                    {{ $religions->firstItem() ?? 0 }}
                    to
                    {{ $religions->lastItem() ?? 0 }}
                    of
                    {{ $religions->total() }}
                    entries

                </div>

                <div>
                    {{ $religions->links() }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    ADD RELIGION MODAL
========================================================= --}}

<div
    class="modal fade"
    id="addReligionModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('admin.religions.store') }}">

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title">
                        Add Religion
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    {{-- Religion --}}
                    <div class="mb-3">

                        <label
                            for="religion"
                            class="form-label">

                            Religion

                        </label>

                        <input
                            type="text"
                            name="religion"
                            id="religion"
                            class="form-control"
                            placeholder="Enter religion"
                            value="{{ old('religion') }}"
                            required
                            maxlength="255">

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
                        Add Religion

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection