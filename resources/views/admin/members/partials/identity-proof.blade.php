{{-- Identity Proof --}}
<div class="card border-0 shadow-sm mb-4 member-section">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-person-vcard me-2"></i>
            Identity Proof
        </h5>
    </div>
    <div class="card-body">
        @php
            $proofPath = 'id_proofs/'.basename((string) $member->id_proof);
            $hasProof = $member->id_proof && \Illuminate\Support\Facades\Storage::disk('public')->exists($proofPath);
            $proofUrl = $hasProof ? \Illuminate\Support\Facades\Storage::disk('public')->url($proofPath) : null;
        @endphp

        <div class="row g-4 align-items-start">
            <div class="col-md-4">
                <div id="idProofPreviewContainer" class="{{ $hasProof ? '' : 'd-none' }}">
                    <img id="idProofPreview" src="{{ $proofUrl ?? '' }}" alt="Identity proof"
                        class="img-fluid rounded border" style="max-height:260px;object-fit:contain;">
                </div>
                @unless($hasProof)
                    <div id="idProofEmpty" class="text-muted border rounded p-4 text-center">
                        <i class="bi bi-file-earmark-person fs-1"></i>
                        <div class="mt-2">No identity proof uploaded.</div>
                    </div>
                @endunless
            </div>
            <div class="col-md-8">
                <label for="id_proof" class="form-label">{{ $hasProof ? 'Replace Identity Proof' : 'Upload Identity Proof' }}</label>
                <input type="file" name="id_proof" id="id_proof" form="member-edit-form"
                    class="form-control @error('id_proof') is-invalid @enderror"
                    accept=".jpg,.jpeg,.png,.webp">
                <div class="form-text">JPG, JPEG, PNG or WebP. Maximum size: 5 MB.</div>
                @error('id_proof')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if($hasProof)
                    <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary mt-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i>View Current Proof
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
