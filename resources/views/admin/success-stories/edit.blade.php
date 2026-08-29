@extends('admin.layout')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Edit Success Story</h2>
            <p class="text-muted mb-0">
                Update this success story.
            </p>
        </div>

        <a href="{{ route('admin.success-stories.index') }}"
            class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>Success Story Details</strong>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.success-stories.update', $story->id) }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="row g-4">

                    {{-- Groom --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Groom Name
                        </label>

                        <input type="text"
                            name="groom_name"
                            class="form-control"
                            value="{{ old('groom_name', $story->groom_name) }}"
                            required>
                    </div>

                    {{-- Bride --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Bride Name
                        </label>

                        <input type="text"
                            name="bride_name"
                            class="form-control"
                            value="{{ old('bride_name', $story->bride_name) }}"
                            required>
                    </div>

                    {{-- Detail --}}
                    <div class="col-12">
                        <label class="form-label">
                            Story
                        </label>

                        <textarea
                            name="detail"
                            id="detail"
                            class="form-control"
                            rows="8"
                            required>{{ old('detail', $story->detail) }}</textarea>
                    </div>

                    {{-- Existing photo --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Current Photo
                        </label>

                        @if($story->photo)
                        <div class="mb-3">
                            <img src="{{ asset('images/success_stories/' . $story->photo) }}"
                                alt="Success Story"
                                style="
                                         width: 180px;
                                         height: 180px;
                                         object-fit: cover;
                                         border-radius: 12px;
                                         border: 1px solid var(--app-border);
                                     ">
                        </div>
                        @else
                        <p class="text-muted">
                            No photo uploaded.
                        </p>
                        @endif

                    </div>

                    {{-- New photo --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Replace Photo
                        </label>

                        <input type="file"
                            name="photo"
                            class="form-control"
                            accept="image/*">

                        <small class="text-muted">
                            Leave empty to keep the existing photo.
                        </small>

                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status"
                            class="form-select">

                            <option value="1"
                                {{ old('status', $story->status) == 1 ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ old('status', $story->status) == 0 ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>

                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('admin.success-stories.index') }}"
                        class="btn btn-light">
                        Cancel
                    </a>

                    <button type="submit"
                        class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>
                        Update Success Story
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection
@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const editorElement = document.querySelector('#detail');

        if (editorElement) {
            ClassicEditor
                .create(editorElement)
                .then(editor => {
                    console.log('CKEditor loaded successfully');
                })
                .catch(error => {
                    console.error('CKEditor error:', error);
                });
        }

    });
</script>
@endpush