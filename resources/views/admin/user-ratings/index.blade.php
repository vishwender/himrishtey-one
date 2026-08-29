@extends('admin.layout')

@section('title', 'User Ratings')

@push('styles')
@vite('resources/css/admin/user-rating/user-rating.css')
@endpush

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">User Ratings</h2>
            <p class="text-muted mb-0">
                View and manage feedback submitted by users.
            </p>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
    @endif

    {{-- Search --}}
    <div class="card mb-4">
        <div class="card-body">

            <form method="GET" action="{{ route('admin.user-ratings.index') }}">

                <div class="row g-3 align-items-end">

                    <div class="col-md-6">
                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Name, email, profile ID or feedback...">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            Rating
                        </label>

                        <input
                            type="text"
                            name="rating"
                            class="form-control"
                            value="{{ request('rating') }}"
                            placeholder="e.g. 2.5">
                    </div>

                    <div class="col-md-3 d-flex gap-2">

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>

                        <a
                            href="{{ route('admin.user-ratings.index') }}"
                            class="btn btn-light border">
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>
    </div>

    {{-- Ratings --}}
    <div class="card">

        <div class="card-header bg-transparent">
            <div class="d-flex justify-content-between align-items-center">

                <strong>
                    Ratings
                </strong>

                <span class="text-muted">
                    {{ $ratings->total() }} total
                </span>

            </div>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Profile ID</th>
                            <th>Rating</th>
                            <th>Feedback</th>
                            <th>Submitted On</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($ratings as $rating)

                        <tr>

                            <td>
                                {{ $rating->id }}
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $rating->name }}
                                </div>

                                <small class="text-muted">
                                    {{ $rating->email }}
                                </small>
                            </td>

                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $rating->profile_id }}
                                </span>
                            </td>

                            <td>
                                @include('admin.user-ratings.partials.stars', [
                                    'value' => $rating->rating,
                                ])
                            </td>

                            <td>
                                <div class="rating-description">
                                    {{ \Illuminate\Support\Str::limit($rating->description, 80) }}
                                </div>
                            </td>

                            <td>
                                {{ $rating->submitted_on }}
                            </td>

                            <td class="text-end">

                                <div class="d-inline-flex gap-1">

                                    <a
                                        href="{{ route('admin.user-ratings.show', $rating->id) }}"
                                        class="btn btn-sm btn-light border"
                                        title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.user-ratings.destroy', $rating->id) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this rating?');">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-light border text-danger"
                                            title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="7" class="text-center py-5">

                                <div class="text-muted">
                                    <i class="bi bi-star fs-2 d-block mb-2"></i>

                                    No ratings found.
                                </div>

                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if($ratings->hasPages())

        <div class="card-footer bg-transparent">
            {{ $ratings->links() }}
        </div>

        @endif

    </div>

</div>

@endsection
