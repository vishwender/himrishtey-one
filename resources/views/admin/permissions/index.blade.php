@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h5 class="mb-1">Permissions</h5>

                    <div
                        class="border-bottom"
                        style="width:60px;">
                    </div>
                </div>

                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addPermissionModal">

                    <i class="bi bi-plus-lg me-1"></i>
                    Add Permission

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


            {{-- Error --}}
            @if(session('error'))

            <div class="alert alert-danger alert-dismissible fade show">

                <i class="bi bi-exclamation-circle me-2"></i>

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
                        action="{{ route('admin.permissions.index') }}">

                        <div class="input-group">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search permissions..."
                                value="{{ $search }}">

                            <button
                                class="btn btn-outline-secondary"
                                type="submit">

                                <i class="bi bi-search"></i>

                            </button>

                            @if($search)

                            <a
                                href="{{ route('admin.permissions.index') }}"
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

                            <th style="width:80px;">
                                ID
                            </th>

                            <th>
                                Permission
                            </th>

                            <th>
                                Slug
                            </th>

                            <th>
                                Description
                            </th>

                            <th style="width:160px;">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($permissions as $permission)

                        <tr>

                            <td>
                                {{ $permission->id }}
                            </td>

                            <td>

                                <span class="fw-medium">
                                    {{ $permission->name }}
                                </span>

                            </td>

                            <td>

                                <code>
                                    {{ $permission->slug }}
                                </code>

                            </td>

                            <td>

                                @if($permission->description)

                                <span>
                                    {{ $permission->description }}
                                </span>

                                @else

                                <span class="text-muted">
                                    -
                                </span>

                                @endif

                            </td>

                            <td>

                                <div class="d-flex gap-2">

                                    {{-- Edit --}}
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editPermissionModal{{ $permission->id }}"
                                        title="Edit">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>


                                    {{-- Delete --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.permissions.destroy',
                                                $permission->id
                                            ) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this permission?');">

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
                            id="editPermissionModal{{ $permission->id }}"
                            tabindex="-1"
                            aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered">

                                <div class="modal-content">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.permissions.update',
                                                $permission->id
                                            ) }}">

                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Edit Permission
                                            </h5>

                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                            </button>

                                        </div>


                                        <div class="modal-body">

                                            {{-- Name --}}
                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Permission Name
                                                </label>

                                                <input
                                                    type="text"
                                                    name="name"
                                                    class="form-control"
                                                    value="{{ $permission->name }}"
                                                    maxlength="255"
                                                    required>

                                            </div>


                                            {{-- Slug --}}
                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Slug
                                                </label>

                                                <input
                                                    type="text"
                                                    name="slug"
                                                    class="form-control"
                                                    value="{{ $permission->slug }}"
                                                    maxlength="255">

                                                <div class="form-text">
                                                    Example: members.view
                                                </div>

                                            </div>


                                            {{-- Description --}}
                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Description
                                                </label>

                                                <textarea
                                                    name="description"
                                                    class="form-control"
                                                    rows="3">{{ $permission->description }}</textarea>

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
                                colspan="5"
                                class="text-center py-5">

                                <i class="bi bi-shield-lock fs-1 text-muted"></i>

                                <p class="text-muted mb-0 mt-2">
                                    No permissions found.
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
                    {{ $permissions->firstItem() ?? 0 }}
                    to
                    {{ $permissions->lastItem() ?? 0 }}
                    of
                    {{ $permissions->total() }}
                    entries

                </div>

                <div>
                    {{ $permissions->links() }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- ADD PERMISSION MODAL --}}
<div
    class="modal fade"
    id="addPermissionModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('admin.permissions.store') }}">

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title">
                        Add Permission
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    {{-- Name --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Permission Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="e.g. View Members"
                            maxlength="255"
                            required>

                    </div>


                    {{-- Slug --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Slug
                        </label>

                        <input
                            type="text"
                            name="slug"
                            class="form-control"
                            placeholder="e.g. members.view"
                            maxlength="255">

                        <div class="form-text">
                            Leave blank to generate automatically.
                        </div>

                    </div>


                    {{-- Description --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="3"
                            placeholder="Describe what this permission allows..."></textarea>

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
                        Add Permission

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection