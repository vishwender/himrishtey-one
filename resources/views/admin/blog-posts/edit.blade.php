@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h5 class="mb-1">
                        Edit Blog Post
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
                action="{{ route(
                    'admin.blog-posts.update',
                    $post->id
                ) }}"
                enctype="multipart/form-data">

                @csrf
                @method('PUT')


                {{-- =====================================================
                    BLOG INFORMATION
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
                                value="{{ old('title', $post->title) }}"
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
                                value="{{ old('slug', $post->slug) }}"
                                maxlength="255"
                                required>

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
                                maxlength="500">{{ old(
                                    'excerpt',
                                    $post->excerpt
                                ) }}</textarea>

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
                                rows="12"
                                required>{{ old(
                                    'content',
                                    $post->content
                                ) }}</textarea>

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

                        @if($post->featured_image)

                        <div class="mb-3">

                            <label class="form-label">
                                Current Image
                            </label>

                            <div>

                                <img
                                    src="{{ asset(
                                            'storage/blog/' .
                                            $post->featured_image
                                        ) }}"
                                    alt="{{ $post->title }}"
                                    style="
                                            width:250px;
                                            height:160px;
                                            object-fit:cover;
                                            border-radius:8px;
                                        ">

                            </div>

                        </div>

                        @endif


                        <div class="mb-3">

                            <label class="form-label">
                                Replace Featured Image
                            </label>

                            <input
                                type="file"
                                name="featured_image"
                                id="featured_image"
                                class="form-control"
                                accept="image/jpeg,image/png,image/webp">

                            <div class="form-text">

                                Leave empty to keep the current image.

                            </div>

                        </div>


                        {{-- New Image Preview --}}
                        <div
                            id="imagePreviewContainer"
                            class="mt-3 d-none">

                            <p class="small text-muted mb-2">
                                New Image Preview
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
                                value="{{ old(
                                    'meta_title',
                                    $post->meta_title
                                ) }}"
                                maxlength="255">

                        </div>


                        {{-- Meta description --}}
                        <div>

                            <label class="form-label">
                                Meta Description
                            </label>

                            <textarea
                                name="meta_description"
                                class="form-control"
                                rows="3"
                                maxlength="255">{{ old(
                                    'meta_description',
                                    $post->meta_description
                                ) }}</textarea>

                        </div>

                    </div>

                </div>


                {{-- =====================================================
                    PUBLISHING
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
                                {{ old(
                                    'is_published',
                                    $post->is_published
                                ) ? 'checked' : '' }}>

                            <label
                                class="form-check-label"
                                for="is_published">

                                Published

                            </label>

                        </div>


                        @if($post->published_at)

                        <div class="small text-muted mt-2">

                            Published on
                            {{ $post->published_at->format(
                                    'd M Y h:i A'
                                ) }}

                        </div>

                        @else

                        <div class="small text-muted mt-2">

                            This post has not been published yet.

                        </div>

                        @endif

                    </div>

                </div>


                {{-- =====================================================
                    ACTIONS
                ====================================================== --}}

                <div class="d-flex justify-content-between">

                    {{-- Delete --}}
                    <button
                        type="button"
                        class="btn btn-outline-danger"
                        onclick="deletePost()">

                        <i class="bi bi-trash me-1"></i>
                        Delete

                    </button>


                    <div class="d-flex gap-2">

                        <a
                            href="{{ route(
                                'admin.blog-posts.index'
                            ) }}"
                            class="btn btn-light">

                            Cancel

                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-check-circle me-1"></i>

                            Update Blog Post

                        </button>

                    </div>

                </div>

            </form>


            {{-- Hidden Delete Form --}}
            <form
                id="deletePostForm"
                method="POST"
                action="{{ route(
                    'admin.blog-posts.destroy',
                    $post->id
                ) }}"
                style="display:none;">

                @csrf
                @method('DELETE')

            </form>

        </div>

    </div>

</div>


{{-- =========================================================
    CKEDITOR + JAVASCRIPT
========================================================= --}}

@push('scripts')

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        /*
        |--------------------------------------------------------------------------
        | CKEditor
        |--------------------------------------------------------------------------
        */

        ClassicEditor
            .create(document.querySelector('#content'))
            .catch(error => {

                console.error(error);

            });


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
            document.getElementById(
                'imagePreviewContainer'
            );


        if (imageInput) {

            imageInput.addEventListener('change', function() {

                const file = this.files[0];

                if (!file) {

                    imagePreviewContainer.classList.add('d-none');

                    return;

                }


                const reader = new FileReader();

                reader.onload = function(event) {

                    imagePreview.src =
                        event.target.result;

                    imagePreviewContainer
                        .classList
                        .remove('d-none');

                };

                reader.readAsDataURL(file);

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Slug
        |--------------------------------------------------------------------------
        */

        const slug =
            document.getElementById('slug');


        if (slug) {

            slug.addEventListener('input', function() {

                this.value = this.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');

            });

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    function deletePost() {
        if (
            confirm(
                'Are you sure you want to permanently delete this blog post?'
            )
        ) {

            document
                .getElementById('deletePostForm')
                .submit();

        }
    }
</script>

@endpush

@endsection