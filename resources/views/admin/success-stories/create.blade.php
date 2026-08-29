@extends('admin.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Add Success Story</h2>
        <p class="text-muted mb-0">
            Add a new couple success story.
        </p>
    </div>

    <a href="{{ route('admin.success-stories.index') }}"
        class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Back
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <strong>Please fix the following errors:</strong>

    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form
    action="{{ route('admin.success-stories.store') }}"
    method="POST"
    enctype="multipart/form-data">
    @csrf

    <div class="card">

        <div class="card-header bg-transparent">
            <strong>Success Story Details</strong>
        </div>

        <div class="card-body">

            <div class="row g-4">

                {{-- Groom --}}
                <div class="col-md-6">

                    <label for="groom_name" class="form-label">
                        Groom Name
                    </label>

                    <input
                        type="text"
                        id="groom_name"
                        name="groom_name"
                        class="form-control @error('groom_name') is-invalid @enderror"
                        value="{{ old('groom_name') }}"
                        placeholder="Enter groom name"
                        required>

                    @error('groom_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror

                </div>

                {{-- Bride --}}
                <div class="col-md-6">

                    <label for="bride_name" class="form-label">
                        Bride Name
                    </label>

                    <input
                        type="text"
                        id="bride_name"
                        name="bride_name"
                        class="form-control @error('bride_name') is-invalid @enderror"
                        value="{{ old('bride_name') }}"
                        placeholder="Enter bride name"
                        required>

                    @error('bride_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror

                </div>

                {{-- Photo --}}
                <div class="col-md-6">

                    <label for="photo" class="form-label">
                        Couple Photo
                    </label>

                    <input
                        type="file"
                        id="photo"
                        name="photo"
                        class="form-control @error('photo') is-invalid @enderror"
                        accept=".jpg,.jpeg,.png,.webp">

                    <div class="form-text">
                        JPG, JPEG, PNG or WEBP. Maximum 5 MB.
                    </div>

                    @error('photo')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror

                    <div id="photoPreview" class="mt-3 d-none">
                        <img
                            id="previewImage"
                            src=""
                            alt="Photo Preview"
                            class="rounded-3"
                            style="max-width: 220px; max-height: 220px; object-fit: cover;">
                    </div>

                </div>

                {{-- Status --}}
                <div class="col-md-6">

                    <label class="form-label d-block">
                        Status
                    </label>

                    <div class="form-check form-switch mt-2">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            id="status"
                            name="status"
                            value="1"
                            {{ old('status', 1) ? 'checked' : '' }}>

                        <label
                            class="form-check-label"
                            for="status">
                            Active
                        </label>

                    </div>

                    <div class="form-text">
                        Active stories can be displayed on the website.
                    </div>

                </div>

                {{-- Detail --}}
                <div class="col-12">

                    <label for="detail" class="form-label">
                        Success Story
                    </label>

                    <textarea
                        id="detail"
                        name="detail"
                        class="@error('detail') is-invalid @enderror"
                        required>{{ old('detail') }}</textarea>

                    @error('detail')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror

                </div>

            </div>

        </div>

        <div class="card-footer bg-transparent d-flex justify-content-end gap-2">

            <a
                href="{{ route('admin.success-stories.index') }}"
                class="btn btn-outline-secondary">
                Cancel
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>
                Save Success Story
            </button>

        </div>

    </div>

</form>

@endsection

@push('scripts')

{{-- CKEditor --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        ClassicEditor
            .create(document.querySelector('#detail'), {
                toolbar: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'underline',
                    '|',
                    'bulletedList',
                    'numberedList',
                    '|',
                    'link',
                    'blockQuote',
                    '|',
                    'undo',
                    'redo'
                ]
            })
            .catch(error => {
                console.error(error);
            });

        const photoInput = document.getElementById('photo');
        const previewContainer = document.getElementById('photoPreview');
        const previewImage = document.getElementById('previewImage');

        photoInput.addEventListener('change', function() {

            const file = this.files[0];

            if (!file) {
                previewContainer.classList.add('d-none');
                previewImage.src = '';
                return;
            }

            if (!file.type.startsWith('image/')) {
                previewContainer.classList.add('d-none');
                return;
            }

            const reader = new FileReader();

            reader.onload = function(event) {
                previewImage.src = event.target.result;
                previewContainer.classList.remove('d-none');
            };

            reader.readAsDataURL(file);
        });

    });
</script>

@endpush