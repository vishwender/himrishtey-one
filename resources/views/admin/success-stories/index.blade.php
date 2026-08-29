@extends('admin.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Success Stories</h2>
        <p class="text-muted mb-0">
            Manage customer success stories and testimonials.
        </p>
    </div>

    <a href="{{ route('admin.success-stories.create') }}"
        class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>
        Add Success Story
    </a>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="card">
    <div class="card-header bg-transparent">
        <div class="d-flex justify-content-between align-items-center">
            <strong>All Success Stories</strong>

            <span class="text-muted small">
                {{ $stories->total() }} stories
            </span>
        </div>
    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>Couple</th>
                        <th>Story</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($stories as $story)

                    <tr>

                        <td>
                            {{ $story->id }}
                        </td>

                        <td>

                            @if($story->photo)

                            <img
                                src="{{ asset('storage/success-stories/' . $story->photo) }}"
                                alt="Success Story"
                                width="60"
                                height="60"
                                class="rounded-3 object-fit-cover">

                            @else

                            <div
                                class="d-flex align-items-center justify-content-center rounded-3 bg-light"
                                style="width:60px;height:60px;">
                                <i class="bi bi-heart text-muted"></i>
                            </div>

                            @endif

                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ $story->groom_name }}
                                <span class="text-muted mx-1">&</span>
                                {{ $story->bride_name }}
                            </div>
                        </td>

                        <td style="max-width: 400px;">
                            <div class="text-truncate">
                                {{ $story->detail }}
                            </div>
                        </td>

                        <td>

                            @if($story->status)

                            <span class="badge bg-success">
                                Active
                            </span>

                            @else

                            <span class="badge bg-secondary">
                                Inactive
                            </span>

                            @endif

                        </td>

                        <td class="text-end">

                            <div class="d-inline-flex gap-1">

                                <a
                                    href="{{ route('admin.success-stories.edit', $story->id) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form
                                    action="{{ route('admin.success-stories.status', $story->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-warning"
                                        title="Change Status">
                                        <i class="bi bi-power"></i>
                                    </button>

                                </form>

                                <form
                                    action="{{ route('admin.success-stories.destroy', $story->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this success story?');">
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
                        <td colspan="6" class="text-center py-5">

                            <div class="text-muted">

                                <i class="bi bi-heart fs-2 d-block mb-2"></i>

                                No success stories found.

                            </div>

                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    @if($stories->hasPages())

    <div class="card-footer bg-transparent">
        {{ $stories->links() }}
    </div>

    @endif

</div>

@endsection