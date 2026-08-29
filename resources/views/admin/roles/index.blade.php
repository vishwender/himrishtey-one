@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="mb-1">Roles</h1>

            <p class="text-muted mb-0">
                Manage staff roles and their access levels.
            </p>
        </div>

        <a
            href="{{ route('admin.roles.create') }}"
            class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Add Role
        </a>

    </div>


    {{-- Success Message --}}
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


    {{-- Error Message --}}
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


    {{-- Roles Card --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Card Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h5 class="mb-1">
                        All Roles
                    </h5>

                    <div class="text-muted small">
                        {{ $roles->total() }} role(s)
                    </div>
                </div>


                {{-- Search --}}
                <form
                    method="GET"
                    action="{{ route('admin.roles.index') }}">

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ $search }}"
                            placeholder="Search roles..."
                            style="min-width: 260px;">

                        @if($search)

                        <a
                            href="{{ route('admin.roles.index') }}"
                            class="btn btn-outline-secondary"
                            title="Clear search">
                            <i class="bi bi-x-lg"></i>
                        </a>

                        @endif

                        <button
                            type="submit"
                            class="btn btn-primary">
                            Search
                        </button>

                    </div>

                </form>

            </div>


            {{-- Table --}}
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th style="width: 80px;">
                                ID
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Slug
                            </th>

                            <th>
                                Description
                            </th>

                            <th>
                                Staff Users
                            </th>

                            <th style="width: 150px;">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($roles as $role)

                        <tr>

                            {{-- ID --}}
                            <td>
                                {{ $role->id }}
                            </td>


                            {{-- Name --}}
                            <td>

                                <div class="fw-semibold">
                                    {{ $role->name }}
                                </div>

                            </td>


                            {{-- Slug --}}
                            <td>

                                <code>
                                    {{ $role->slug }}
                                </code>

                            </td>


                            {{-- Description --}}
                            <td>

                                @if($role->description)

                                <span>
                                    {{ $role->description }}
                                </span>

                                @else

                                <span class="text-muted">
                                    —
                                </span>

                                @endif

                            </td>


                            {{-- Staff Count --}}
                            <td>

                                <span class="badge bg-primary-subtle text-primary">

                                    <i class="bi bi-people me-1"></i>

                                    {{ $role->admins_count }}

                                </span>

                            </td>


                            {{-- Actions --}}
                            <td>

                                <div class="d-flex gap-2">

                                    {{-- Edit --}}
                                    <a
                                        href="{{ route(
                                                'admin.roles.edit',
                                                $role->id
                                            ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit">

                                        <i class="bi bi-pencil-square"></i>

                                    </a>


                                    {{-- Delete --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.roles.destroy',
                                                $role->id
                                            ) }}"
                                        onsubmit="return confirm(
                                                'Are you sure you want to delete this role?'
                                            );">

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

                        @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-5">

                                <i
                                    class="bi bi-shield-lock fs-1 text-muted"></i>

                                <p class="text-muted mb-0 mt-2">
                                    @if($search)
                                    No roles found for
                                    "{{ $search }}".
                                    @else
                                    No roles found.
                                    @endif
                                </p>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if($roles->hasPages())

            <div class="d-flex justify-content-between align-items-center mt-4">

                <div class="text-muted small">

                    Showing
                    {{ $roles->firstItem() ?? 0 }}
                    to
                    {{ $roles->lastItem() ?? 0 }}
                    of
                    {{ $roles->total() }}
                    entries

                </div>

                <div>
                    {{ $roles->links() }}
                </div>

            </div>

            @endif

        </div>

    </div>

</div>

@endsection