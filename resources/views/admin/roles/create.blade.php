@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="mb-1">Create Role</h1>

            <p class="text-muted mb-0">
                Create a staff role and assign permissions.
            </p>
        </div>

        <a
            href="{{ route('admin.roles.index') }}"
            class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Roles
        </a>

    </div>


    {{-- Validation Errors --}}
    @if($errors->any())

    <div class="alert alert-danger alert-dismissible fade show">

        <i class="bi bi-exclamation-circle me-2"></i>

        <strong>Please fix the following errors:</strong>

        <ul class="mb-0 mt-2">

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


    <form
        method="POST"
        action="{{ route('admin.roles.store') }}">

        @csrf


        {{-- Role Details --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <h5 class="mb-1">
                    Role Details
                </h5>

                <p class="text-muted small mb-4">
                    Basic information about this role.
                </p>


                <div class="row">

                    {{-- Role Name --}}
                    <div class="col-md-6 mb-3">

                        <label
                            for="name"
                            class="form-label">
                            Role Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            placeholder="e.g. Content Manager"
                            maxlength="255"
                            required>

                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                    {{-- Slug --}}
                    <div class="col-md-6 mb-3">

                        <label
                            for="slug"
                            class="form-label">
                            Slug
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="slug"
                            name="slug"
                            class="form-control @error('slug') is-invalid @enderror"
                            value="{{ old('slug') }}"
                            placeholder="e.g. content-manager"
                            maxlength="255"
                            required>

                        <div class="form-text">
                            Use lowercase letters, numbers and hyphens.
                        </div>

                        @error('slug')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                    {{-- Description --}}
                    <div class="col-12 mb-3">

                        <label
                            for="description"
                            class="form-label">
                            Description
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            class="form-control @error('description') is-invalid @enderror"
                            rows="4"
                            placeholder="Describe what this role is responsible for...">{{ old('description') }}</textarea>

                        @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>


        {{-- Permissions --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>

                        <h5 class="mb-1">
                            Permissions
                        </h5>

                        <p class="text-muted small mb-0">
                            Select what this role is allowed to access.
                        </p>

                    </div>

                    <div>

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

                </div>


                @if($permissions->count())

                <div class="row">

                    @foreach($permissions as $permission)

                    <div class="col-md-6 col-lg-4 mb-3">

                        <div class="form-check border rounded p-3 h-100">

                            <input
                                class="form-check-input permission-checkbox"
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->id }}"
                                id="permission{{ $permission->id }}"
                                {{ in_array(
                                            $permission->id,
                                            old('permissions', [])
                                        ) ? 'checked' : '' }}>

                            <label
                                class="form-check-label ms-2"
                                for="permission{{ $permission->id }}">

                                <span class="fw-semibold d-block">
                                    {{ $permission->name }}
                                </span>

                                <small class="text-muted">
                                    {{ $permission->slug }}
                                </small>

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

                @else

                <div class="text-center py-5">

                    <i class="bi bi-shield-lock fs-1 text-muted"></i>

                    <p class="text-muted mt-2 mb-1">
                        No permissions have been created yet.
                    </p>

                    <small class="text-muted">
                        Create permissions before assigning them to roles.
                    </small>

                </div>

                @endif

            </div>

        </div>


        {{-- Actions --}}
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
                Create Role
            </button>

        </div>

    </form>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const checkboxes = document.querySelectorAll(
            '.permission-checkbox'
        );

        const selectAll = document.getElementById(
            'selectAllPermissions'
        );

        const clearAll = document.getElementById(
            'clearAllPermissions'
        );


        selectAll?.addEventListener('click', function() {

            checkboxes.forEach(function(checkbox) {
                checkbox.checked = true;
            });

        });


        clearAll?.addEventListener('click', function() {

            checkboxes.forEach(function(checkbox) {
                checkbox.checked = false;
            });

        });


        /*
         * Automatically generate slug from role name.
         */
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');

        nameInput?.addEventListener('input', function() {

            /*
             * Don't overwrite the slug if the administrator
             * has already manually edited it.
             */
            if (
                slugInput.dataset.manuallyEdited === 'true'
            ) {
                return;
            }

            slugInput.value = nameInput.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');

        });


        slugInput?.addEventListener('input', function() {

            slugInput.dataset.manuallyEdited = 'true';

        });

    });
</script>

@endsection