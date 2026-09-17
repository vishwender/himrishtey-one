@if(($pendingDeleteRequestCount ?? 0) > 0)
    <div class="alert alert-warning d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4" role="status">
        <div>
            <h2 class="h6 mb-1">
                <i class="bi bi-bell-fill me-2" aria-hidden="true"></i>
                Profile deletion requests
                <span class="badge text-bg-warning ms-1">{{ number_format($pendingDeleteRequestCount) }}</span>
            </h2>
            <p class="mb-0">
                {{ $pendingDeleteRequestCount === 1 ? 'A profile deletion request is awaiting review.' : 'Profile deletion requests are awaiting review.' }}
            </p>
        </div>
        <a href="{{ route('admin.members.delete-requests.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-dark">
            Review delete requests <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
        </a>
    </div>
@endif
