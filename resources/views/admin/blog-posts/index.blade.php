@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h5 class="mb-1">
                        Blog Posts
                    </h5>

                    <div
                        class="border-bottom"
                        style="width:60px;">
                    </div>
                </div>

                <a
                    href="{{ route('admin.blog-posts.create') }}"
                    class="btn btn-primary">

                    <i class="bi bi-plus-lg me-1"></i>
                    Create Blog Post

                </a>

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
                        action="{{ route('admin.blog-posts.index') }}">

                        <div class="input-group">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search blog posts..."
                                value="{{ $search }}">

                            <button
                                class="btn btn-outline-secondary"
                                type="submit">

                                <i class="bi bi-search"></i>

                            </button>

                            @if($search)

                            <a
                                href="{{ route('admin.blog-posts.index') }}"
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

                            <th style="width:70px;">
                                ID
                            </th>

                            <th style="width:100px;">
                                Image
                            </th>

                            <th>
                                Title
                            </th>

                            <th>
                                Slug
                            </th>

                            <th style="width:130px;">
                                Status
                            </th>

                            <th style="width:160px;">
                                Published
                            </th>

                            <th style="width:190px;">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($posts as $post)

                        <tr>

                            <td>
                                {{ $post->id }}
                            </td>


                            {{-- Image --}}
                            <td>

                                @if($post->featured_image)

                                <img
                                    src="{{ asset('storage/blog/' . $post->featured_image) }}"
                                    alt="{{ $post->title }}"
                                    style="
                                                width:70px;
                                                height:50px;
                                                object-fit:cover;
                                                border-radius:6px;
                                            ">

                                @else

                                <div
                                    class="d-flex align-items-center justify-content-center bg-light text-muted"
                                    style="
                                                width:70px;
                                                height:50px;
                                                border-radius:6px;
                                            ">

                                    <i class="bi bi-image"></i>

                                </div>

                                @endif

                            </td>


                            {{-- Title --}}
                            <td>

                                <div class="fw-medium">
                                    {{ $post->title }}
                                </div>

                                @if($post->excerpt)

                                <small class="text-muted">

                                    {{ Str::limit($post->excerpt, 70) }}

                                </small>

                                @endif

                            </td>


                            {{-- Slug --}}
                            <td>

                                <code>
                                    {{ $post->slug }}
                                </code>

                            </td>


                            {{-- Status --}}
                            <td>

                                @if($post->is_published)

                                <span class="badge bg-success">
                                    Published
                                </span>

                                @else

                                <span class="badge bg-secondary">
                                    Draft
                                </span>

                                @endif

                            </td>


                            {{-- Published --}}
                            <td>

                                @if($post->published_at)

                                <span class="small">

                                    {{ $post->published_at->format('d M Y') }}

                                    <br>

                                    <span class="text-muted">
                                        {{ $post->published_at->format('h:i A') }}
                                    </span>

                                </span>

                                @else

                                <span class="text-muted">
                                    Not published
                                </span>

                                @endif

                            </td>


                            {{-- Actions --}}
                            <td>

                                <div class="d-flex gap-2 flex-wrap">

                                    {{-- Edit --}}
                                    <a
                                        href="{{ route(
                                                'admin.blog-posts.edit',
                                                $post->id
                                            ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit">

                                        <i class="bi bi-pencil-square"></i>

                                    </a>


                                    {{-- Publish / Unpublish --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.blog-posts.toggle-publish',
                                                $post->id
                                            ) }}">

                                        @csrf
                                        @method('PATCH')

                                        @if($post->is_published)

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Unpublish">

                                            <i class="bi bi-eye-slash"></i>

                                        </button>

                                        @else

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-success"
                                            title="Publish">

                                            <i class="bi bi-eye"></i>

                                        </button>

                                        @endif

                                    </form>


                                    {{-- Delete --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.blog-posts.destroy',
                                                $post->id
                                            ) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this blog post?');">

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
                                colspan="7"
                                class="text-center py-5">

                                <i
                                    class="bi bi-journal-text fs-1 text-muted">
                                </i>

                                <p class="text-muted mb-0 mt-2">

                                    @if($search)

                                    No blog posts found matching
                                    "{{ $search }}".

                                    @else

                                    No blog posts found.

                                    @endif

                                </p>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if($posts->hasPages() || $posts->total())

            <div class="d-flex justify-content-between align-items-center mt-3">

                <div class="text-muted small">

                    Showing
                    {{ $posts->firstItem() ?? 0 }}
                    to
                    {{ $posts->lastItem() ?? 0 }}
                    of
                    {{ $posts->total() }}
                    entries

                </div>

                <div>

                    {{ $posts->links() }}

                </div>

            </div>

            @endif

        </div>

    </div>

</div>

@endsection