@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h5 class="mb-1">
                        Casts
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
                    data-bs-target="#addCastModal">

                    <i class="bi bi-plus-lg me-1"></i>
                    Add Cast

                </button>

            </div>


            {{-- Success --}}
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


            {{-- Search --}}
            <div class="row mb-3">

                <div class="col-md-4 ms-auto">

                    <form
                        method="GET"
                        action="{{ route('admin.casts.index') }}">

                        <div class="input-group">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search cast or religion..."
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


            {{-- Table --}}
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th style="width:120px;">
                                ID
                            </th>

                            <th>
                                Cast
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

                        @forelse($casts as $cast)

                        <tr>

                            <td>
                                {{ $cast->id }}
                            </td>

                            <td>
                                {{ $cast->cast }}
                            </td>

                            <td>
                                {{ $cast->religion ?: '-' }}
                            </td>

                            <td>

                                <div class="d-flex gap-2">

                                    {{-- Edit --}}
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editCastModal{{ $cast->id }}">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>


                                    {{-- Delete --}}
                                    <form
                                        action="{{ route(
                                                'admin.casts.destroy',
                                                $cast->id
                                            ) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this cast?');">

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


                        {{-- Edit Modal --}}
                        <div
                            class="modal fade"
                            id="editCastModal{{ $cast->id }}"
                            tabindex="-1"
                            aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered">

                                <div class="modal-content">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.casts.update',
                                                $cast->id
                                            ) }}">

                                        @csrf
                                        @method('PUT')


                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Edit Cast
                                            </h5>

                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                            </button>

                                        </div>


                                        <div class="modal-body">

                                            {{-- Cast --}}
                                            <div class="mb-3">

                                                <label
                                                    class="form-label">

                                                    Cast

                                                </label>

                                                <input
                                                    type="text"
                                                    name="cast"
                                                    class="form-control"
                                                    value="{{ $cast->cast }}"
                                                    required
                                                    maxlength="255">

                                            </div>


                                            {{-- Religion --}}
                                            <div class="mb-3">

                                                <label
                                                    class="form-label">

                                                    Religion

                                                </label>

                                                <select
                                                    name="religion"
                                                    class="form-select"
                                                    required>

                                                    <option value="">
                                                        Select Religion
                                                    </option>

                                                    @foreach($religions as $religion)

                                                    <option
                                                        value="{{ $religion->religion }}"
                                                        {{ $cast->religion === $religion->religion ? 'selected' : '' }}>

                                                        {{ $religion->religion }}

                                                    </option>

                                                    @endforeach

                                                </select>

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
                                colspan="4"
                                class="text-center py-5">

                                <i
                                    class="bi bi-people fs-1 text-muted">
                                </i>

                                <p class="text-muted mb-0 mt-2">
                                    No cast records found.
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
                    {{ $casts->firstItem() ?? 0 }}
                    to
                    {{ $casts->lastItem() ?? 0 }}
                    of
                    {{ $casts->total() }}
                    entries

                </div>


                <div>
                    {{ $casts->links() }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    ADD CAST MODAL
========================================================= --}}

<div
    class="modal fade"
    id="addCastModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('admin.casts.store') }}">

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title">
                        Add Cast
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    {{-- Cast --}}
                    <div class="mb-3">

                        <label
                            for="cast"
                            class="form-label">

                            Cast

                        </label>

                        <input
                            type="text"
                            name="cast"
                            id="cast"
                            class="form-control"
                            placeholder="Enter cast"
                            required
                            maxlength="255">

                    </div>


                    {{-- Religion --}}
                    <div class="mb-3">

                        <label
                            for="religion"
                            class="form-label">

                            Religion

                        </label>

                        <select
                            name="religion"
                            id="religion"
                            class="form-select"
                            required>

                            <option value="">
                                Select Religion
                            </option>

                            @foreach($religions as $religion)

                            <option
                                value="{{ $religion->religion }}">

                                {{ $religion->religion }}

                            </option>

                            @endforeach

                        </select>

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
                        Add Cast

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection