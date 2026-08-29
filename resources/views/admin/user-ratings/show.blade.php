@extends('admin.layout')

@section('title', 'Rating Details')

@push('styles')
@vite('resources/css/admin/user-rating/user-rating.css')
@endpush

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="mb-1">Rating Details</h2>

            <p class="text-muted mb-0">
                User feedback information
            </p>
        </div>

        <a
            href="{{ route('admin.user-ratings.index') }}"
            class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>

    </div>

    <div class="row g-4">

        <div class="col-lg-8">

            <div class="card">

                <div class="card-header bg-transparent">
                    <strong>User Feedback</strong>
                </div>

                <div class="card-body">

                    <div class="mb-4">

                        <label class="form-label text-muted">
                            Feedback
                        </label>

                        <div class="rating-feedback">
                            {{ $rating->description }}
                        </div>

                    </div>

                    <div class="row g-4">

                        <div class="col-md-6">

                            <label class="form-label text-muted">
                                Rating
                            </label>

                            <div class="rating-stars-large">
                                @include('admin.user-ratings.partials.stars', [
                                    'value' => $rating->rating,
                                ])
                            </div>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label text-muted">
                                Submitted On
                            </label>

                            <div>
                                {{ $rating->submitted_on }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card">

                <div class="card-header bg-transparent">
                    <strong>User Information</strong>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            Name
                        </small>

                        <strong>
                            {{ $rating->name }}
                        </strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            Email
                        </small>

                        <span>
                            {{ $rating->email }}
                        </span>
                    </div>

                    <div>
                        <small class="text-muted d-block">
                            Profile ID
                        </small>

                        <span class="badge bg-light text-dark">
                            {{ $rating->profile_id }}
                        </span>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
