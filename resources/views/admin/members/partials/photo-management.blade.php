<div id="photo-management" class="card border-0 shadow-sm mb-5 member-section">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-camera me-2"></i>
            Photo Management
        </h5>
        <p class="text-muted small mb-0 mt-2">Manage the member's profile and gallery photos.</p>
    </div>

    <div class="card-body">
        @if($errors->has('photo'))
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first('photo') }}
            </div>
        @endif

        <div class="row g-4 align-items-start mb-4">
            <div class="col-md-4 text-center">
                <h6 class="text-muted mb-3">Current Profile Photo</h6>
                @if(!empty($member->photo))
                    <img src="{{ $member->photo_url }}" alt="{{ $member->full_name }}"
                        class="rounded object-fit-cover" style="width:180px;height:180px;" loading="lazy">
                @else
                    <div class="mx-auto rounded bg-light d-flex align-items-center justify-content-center"
                        style="width:180px;height:180px;">
                        <i class="bi bi-person fs-1 text-muted"></i>
                    </div>
                @endif
            </div>

            <div class="col-md-8">
                <div class="border rounded p-4">
                    <h6 class="mb-3"><i class="bi bi-cloud-arrow-up me-2"></i>Upload Gallery Photo</h6>
                    <form action="{{ route('admin.members.photos.store', ['memberId' => $member->id]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="member_photo" class="form-label">Select Photo</label>
                        <input type="file" class="form-control" id="member_photo" name="photo"
                            accept=".jpg,.jpeg,.png,.webp" required>
                        <div class="form-text mb-3">JPG, JPEG, PNG or WebP. Maximum size: 10 MB.</div>
                        <div id="photoPreviewContainer" class="mb-3 d-none">
                            <img id="photoPreview" src="" alt="Photo preview" class="rounded object-fit-cover"
                                style="width:180px;height:180px;">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-1"></i>Upload Photo
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <hr class="my-4">
        <h6 class="mb-3"><i class="bi bi-images me-2"></i>Gallery</h6>

        @if($galleryPhotos->count())
            <div class="row g-3">
                @foreach($galleryPhotos as $photo)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card border h-100">
                            <div class="position-relative">
                                <img src="{{ $photo->photo_url }}" alt="{{ $member->full_name }} photo"
                                    class="card-img-top" style="height:180px;object-fit:cover;" loading="lazy">
                                @if($member->photo === $photo->photo)
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-success">Profile</span>
                                @endif
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Photo #{{ $photo->id }}</small>
                                    <span class="badge {{ $photo->photo_approved === 'Yes' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $photo->photo_approved === 'Yes' ? 'Approved' : 'Pending' }}
                                    </span>
                                </div>
                                <small class="text-muted d-block mb-3">
                                    <i class="bi {{ $photo->photo_privacy == 1 ? 'bi-globe' : 'bi-lock' }} me-1"></i>
                                    {{ $photo->photo_privacy == 1 ? 'Public' : 'Private' }}
                                </small>

                                <form action="{{ route($photo->photo_approved === 'Yes' ? 'admin.members.photos.unapprove' : 'admin.members.photos.approve', ['memberId' => $member->id, 'photoId' => $photo->id]) }}" method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $photo->photo_approved === 'Yes' ? 'btn-outline-danger' : 'btn-outline-success' }} w-100">
                                        {{ $photo->photo_approved === 'Yes' ? 'Reject Photo' : 'Approve Photo' }}
                                    </button>
                                </form>

                                @if($member->photo === $photo->photo)
                                    <button type="button" class="btn btn-sm btn-success w-100" disabled>Current Profile Photo</button>
                                @else
                                    <form action="{{ route('admin.members.photos.set-profile', ['memberId' => $member->id, 'photoId' => $photo->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">Set as Profile</button>
                                    </form>
                                    <form action="{{ route('admin.members.photos.destroy', ['memberId' => $member->id, 'photoId' => $photo->id]) }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100"
                                            onclick="return confirm('Are you sure you want to permanently delete this photo?')">Delete Photo</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-images fs-1"></i>
                <p class="mt-2 mb-0">No gallery photos found.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('member_photo');
        const container = document.getElementById('photoPreviewContainer');
        const preview = document.getElementById('photoPreview');

        input?.addEventListener('change', function () {
            const file = this.files?.[0];
            if (!file) {
                container?.classList.add('d-none');
                return;
            }
            preview.src = URL.createObjectURL(file);
            container?.classList.remove('d-none');
        });
    });
</script>
@endpush
