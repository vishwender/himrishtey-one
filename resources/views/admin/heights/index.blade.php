@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h5 class="mb-1">Heights</h5>

                    <div
                        class="border-bottom"
                        style="width:60px;">
                    </div>
                </div>

                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addHeightModal">

                    <i class="bi bi-plus-lg me-1"></i>
                    Add Height

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

                <div class="col-md-4 ms-auto">

                    <form
                        method="GET"
                        action="{{ route('admin.heights.index') }}">

                        <div class="input-group">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search height..."
                                value="{{ $search }}">

                            <button
                                class="btn btn-outline-secondary"
                                type="submit">

                                <i class="bi bi-search"></i>

                            </button>

                            @if($search)

                            <a
                                href="{{ route('admin.heights.index') }}"
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

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th style="width:100px;">
                                ID
                            </th>

                            <th>
                                Height
                            </th>

                            <th>
                                Height Value
                            </th>

                            <th style="width:160px;">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($heights as $height)

                        <tr>

                            <td>
                                {{ $height->id }}
                            </td>

                            <td>

                                <span class="fw-medium">
                                    {{ $height->height }}
                                </span>

                                <small class="text-muted">
                                    ft/in
                                </small>

                            </td>

                            <td>
                                {{ $height->height_value }}
                            </td>

                            <td>

                                <div class="d-flex gap-2">

                                    {{-- Edit --}}
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editHeightModal{{ $height->id }}"
                                        title="Edit">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>


                                    {{-- Delete --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.heights.destroy',
                                                $height->id
                                            ) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this height?');">

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


                        {{-- Edit Modal --}}
                        <div
                            class="modal fade"
                            id="editHeightModal{{ $height->id }}"
                            tabindex="-1"
                            aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered">

                                <div class="modal-content">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.heights.update',
                                                $height->id
                                            ) }}">

                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Edit Height
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
                                                    Height
                                                </label>

                                                <input
                                                    type="text"
                                                    name="height"
                                                    class="form-control"
                                                    value="{{ $height->height }}"
                                                    placeholder="Example: 5.10"
                                                    required>

                                                <small class="text-muted">
                                                    Use feet.inches format.
                                                    Example: 5.10 = 5 ft 10 in.
                                                </small>

                                            </div>


                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Height Value
                                                </label>

                                                <input
                                                    type="text"
                                                    name="height_value"
                                                    class="form-control"
                                                    value="{{ $height->height_value }}"
                                                    placeholder="Example: 5.10"
                                                    required>

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

                                <i class="bi bi-rulers fs-1 text-muted"></i>

                                <p class="text-muted mb-0 mt-2">
                                    No height records found.
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
                    {{ $heights->firstItem() ?? 0 }}
                    to
                    {{ $heights->lastItem() ?? 0 }}
                    of
                    {{ $heights->total() }}
                    entries

                </div>

                <div>
                    {{ $heights->links() }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- ADD MODAL --}}
<div
    class="modal fade"
    id="addHeightModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('admin.heights.store') }}">

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title">
                        Add Height
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
                            Height
                        </label>

                        <input
                            type="text"
                            name="height"
                            class="form-control"
                            placeholder="Example: 5.10"
                            required>

                        <small class="text-muted">
                            Use feet.inches format.
                            Example: 5.10 = 5 ft 10 in.
                        </small>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Height Value
                        </label>

                        <input
                            type="text"
                            name="height_value"
                            class="form-control"
                            placeholder="Example: 5.10"
                            required>

                        <small class="text-muted">
                            Keep this consistent with the height value
                            used by the existing system.
                        </small>

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
                        Add Height

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection