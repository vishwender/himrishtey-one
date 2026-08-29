@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h5 class="mb-1">
                        Create Blog Post
                    </h5>

                    <div
                        class="border-bottom"
                        style="width:60px;">
                    </div>

                </div>

                <a
                    href="{{ route('admin.blog-posts.index') }}"
                    class="btn btn-light">

                    <i class="bi bi-arrow-left me-1"></i>
                    Back

                </a>

            </div>


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


            <form
                method="POST"
                action="{{ route('admin.blog-posts.store') }}"
                enctype="multipart/form-data">

                @csrf


                {{-- =====================================================
                    BASIC INFORMATION
                ====================================================== --}}

                <div class="card border mb-4">

                    <div class="card-header bg-light">

                        <strong>
                            Blog Information
                        </strong>

                    </div>

                    <div class="card-body">

                        {{-- Title --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Title
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="title"
                                id="title"
                                class="form-control"
                                value="{{ old('title') }}"
                                placeholder="Enter blog title"
                                maxlength="255"
                                required>

                        </div>


                        {{-- Slug --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Slug
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="slug"
                                id="slug"
                                class="form-control"
                                value="{{ old('slug') }}"
                                placeholder="blog-post-url"
                                maxlength="255"
                                required>

                            <div class="form-text">

                                Example:
                                <code>relationship-tips-for-couples</code>

                            </div>

                        </div>


                        {{-- Excerpt --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Excerpt
                            </label>

                            <textarea
                                name="excerpt"
                                class="form-control"
                                rows="3"
                                maxlength="500"
                                placeholder="Short description of the blog post...">{{ old('excerpt') }}</textarea>

                        </div>


                        {{-- Content --}}
                        <div class="mb-3">

                            <label class="form-label">

                                Content
                                <span class="text-danger">*</span>

                            </label>

                            <textarea
                                name="content"
                                id="content"
                                class="form-control"
                                rows="12">{{ old('content') }}</textarea>

                        </div>

                    </div>

                </div>


                {{-- =====================================================
                    FEATURED IMAGE
                ====================================================== --}}

                <div class="card border mb-4">

                    <div class="card-header bg-light">

                        <strong>
                            Featured Image
                        </strong>

                    </div>

                    <div class="card-body">

                        <div class="mb-3">

                            <label class="form-label">
                                Featured Image
                            </label>

                            <input
                                type="file"
                                name="featured_image"
                                id="featured_image"
                                class="form-control"
                                accept="image/jpeg,image/png,image/webp">

                            <div class="form-text">

                                Recommended formats:
                                JPG, PNG or WebP.
                                Maximum size: 5 MB.

                            </div>

                        </div>


                        {{-- Preview --}}
                        <div
                            id="imagePreviewContainer"
                            class="mt-3 d-none">

                            <p class="small text-muted mb-2">
                                Preview
                            </p>

                            <img
                                id="imagePreview"
                                src="#"
                                alt="Preview"
                                style="
                                    max-width:300px;
                                    max-height:200px;
                                    object-fit:cover;
                                    border-radius:8px;
                                ">

                        </div>

                    </div>

                </div>


                {{-- =====================================================
                    SEO
                ====================================================== --}}

                <div class="card border mb-4">

                    <div class="card-header bg-light">

                        <strong>
                            SEO Settings
                        </strong>

                    </div>

                    <div class="card-body">

                        {{-- Meta title --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Meta Title
                            </label>

                            <input
                                type="text"
                                name="meta_title"
                                class="form-control"
                                value="{{ old('meta_title') }}"
                                maxlength="255"
                                placeholder="SEO title">

                            <div class="form-text">
                                Recommended: around 50–60 characters.
                            </div>

                        </div>


                        {{-- Meta description --}}
                        <div class="mb-0">

                            <label class="form-label">
                                Meta Description
                            </label>

                            <textarea
                                name="meta_description"
                                class="form-control"
                                rows="3"
                                maxlength="255"
                                placeholder="SEO description">{{ old('meta_description') }}</textarea>

                            <div class="form-text">
                                Recommended: around 150–160 characters.
                            </div>

                        </div>

                    </div>

                </div>


                {{-- =====================================================
                    PUBLISH
                ====================================================== --}}

                <div class="card border mb-4">

                    <div class="card-header bg-light">

                        <strong>
                            Publishing
                        </strong>

                    </div>

                    <div class="card-body">

                        <div class="form-check form-switch">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="is_published"
                                value="1"
                                id="is_published"
                                {{ old('is_published') ? 'checked' : '' }}>

                            <label
                                class="form-check-label"
                                for="is_published">

                                Publish this blog post immediately

                            </label>

                        </div>

                        <div class="form-text mt-2">

                            If disabled, the blog post will be saved as a draft.

                        </div>

                    </div>

                </div>


                {{-- Buttons --}}
                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="{{ route('admin.blog-posts.index') }}"
                        class="btn btn-light">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-check-circle me-1"></i>

                        Save Blog Post

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- =========================================================
    CKEDITOR
========================================================= --}}

@push('scripts')

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        ClassicEditor
            .create(document.querySelector('#content'))
            .catch(error => {
                console.error(error);
            });


        /*
        |--------------------------------------------------------------------------
        | Generate Slug
        |--------------------------------------------------------------------------
        */

        const title = document.getElementById('title');
        const slug = document.getElementById('slug');

        if (title && slug) {

            title.addEventListener('input', function() {

                /*
                 * Only automatically generate slug if
                 * the user has not manually changed it.
                 */
                if (!slug.dataset.edited) {

                    slug.value = this.value
                        .toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');

                }

            });


            slug.addEventListener('input', function() {

                this.dataset.edited = 'true';

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Image Preview
        |--------------------------------------------------------------------------
        */

        const imageInput =
            document.getElementById('featured_image');

        const imagePreview =
            document.getElementById('imagePreview');

        const imagePreviewContainer =
            document.getElementById('imagePreviewContainer');


        if (imageInput) {

            imageInput.addEventListener('change', function() {

                const file = this.files[0];

                if (!file) {

                    imagePreviewContainer.classList.add('d-none');

                    return;

                }


                const reader = new FileReader();

                reader.onload = function(event) {

                    imagePreview.src = event.target.result;

                    imagePreviewContainer.classList.remove('d-none');

                };

                reader.readAsDataURL(file);

            });

        }

    });
</script>

@endpush

@endsection