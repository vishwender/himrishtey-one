@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h5 class="mb-1">Edit Role</h5>

                    <div
                        class="border-bottom"
                        style="width:60px;">
                    </div>
                </div>

                <a
                    href="{{ route('admin.roles.index') }}"
                    class="btn btn-light">

                    <i class="bi bi-arrow-left me-1"></i>
                    Back

                </a>

            </div>


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


            {{-- Edit Role Form --}}
            <form
                method="POST"
                action="{{ route('admin.roles.update', $role->id) }}">

                @csrf
                @method('PUT')


                {{-- Role Information --}}
                <div class="row">

                    {{-- Role Name --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Role Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $role->name) }}"
                            placeholder="e.g. Content Manager"
                            maxlength="255"
                            required>

                    </div>


                    {{-- Role Slug --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Role Slug
                        </label>

                        <input
                            type="text"
                            name="slug"
                            class="form-control"
                            value="{{ old('slug', $role->slug) }}"
                            placeholder="e.g. content-manager"
                            maxlength="255"
                            required>

                        <div class="form-text">
                            Use a unique slug such as
                            <code>content-manager</code>.
                        </div>

                    </div>


                    {{-- Description --}}
                    <div class="col-12 mb-4">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="3"
                            placeholder="Describe what this role is used for...">{{ old('description', $role->description) }}</textarea>

                    </div>

                </div>


                {{-- Permissions --}}
                <div class="mb-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <div>

                            <h6 class="mb-1">
                                Permissions
                            </h6>

                            <small class="text-muted">
                                Select the permissions this role should have.
                            </small>

                        </div>

                        @if($permissions->count())

                        <div class="d-flex gap-2">

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary"
                                id="selectAllPermissions">

                                Select All

                            </button>

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary"
                                id="clearAllPermissions">

                                Clear All

                            </button>

                        </div>

                        @endif

                    </div>


                    @if($permissions->count())

                    <div class="border rounded p-3">

                        <div class="row">

                            @foreach($permissions as $permission)

                            <div class="col-md-6 col-lg-4 mb-3">

                                <div class="form-check">

                                    <input
                                        class="form-check-input permission-checkbox"
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->id }}"
                                        id="permission{{ $permission->id }}"
                                        {{ in_array(
                                                    $permission->id,
                                                    old(
                                                        'permissions',
                                                        $selectedPermissions ?? []
                                                    )
                                                ) ? 'checked' : '' }}>

                                    <label
                                        class="form-check-label"
                                        for="permission{{ $permission->id }}">

                                        <strong>
                                            {{ $permission->name }}
                                        </strong>

                                        <code class="d-block small mt-1">
                                            {{ $permission->slug }}
                                        </code>

                                        @if($permission->description)

                                        <small class="text-muted d-block mt-1">
                                            {{ $permission->description }}
                                        </small>

                                        @endif

                                    </label>

                                </div>

                            </div>

                            @endforeach

                        </div>

                    </div>

                    @else

                    <div class="border rounded p-4 text-center">

                        <i class="bi bi-shield-lock fs-2 text-muted"></i>

                        <p class="text-muted mb-2 mt-2">
                            No permissions have been created yet.
                        </p>

                        <a
                            href="{{ route('admin.permissions.index') }}"
                            class="btn btn-sm btn-outline-primary">

                            Manage Permissions

                        </a>

                    </div>

                    @endif

                </div>


                {{-- Current Role Information --}}
                <div class="alert alert-light border">

                    <div class="d-flex align-items-start">

                        <i class="bi bi-info-circle me-2"></i>

                        <div>

                            <strong>
                                Role:
                            </strong>

                            {{ $role->name }}

                            @if($role->slug)

                            <code class="ms-1">
                                {{ $role->slug }}
                            </code>

                            @endif

                            <div class="small text-muted mt-1">

                                Changing the permissions here will immediately
                                update what staff users assigned to this role
                                are allowed to access.

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Buttons --}}
                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="{{ route('admin.roles.index') }}"
                        class="btn btn-light">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-check-circle me-1"></i>

                        Update Role

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- Permission Selection JS --}}
@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const selectAllButton =
            document.getElementById('selectAllPermissions');

        const clearAllButton =
            document.getElementById('clearAllPermissions');

        const checkboxes =
            document.querySelectorAll('.permission-checkbox');


        if (selectAllButton) {

            selectAllButton.addEventListener('click', function() {

                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = true;
                });

            });

        }


        if (clearAllButton) {

            clearAllButton.addEventListener('click', function() {

                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = false;
                });

            });

        }

    });
</script>

@endpush

@endsection