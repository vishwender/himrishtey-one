@extends('admin.layout')

@section('title', 'Activate Member')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3">Activate Member</h1>
            <p class="text-muted mb-0">{{ $member->full_name }} · {{ $member->profile_id }}</p>
        </div>
        <a href="{{ route('admin.members.show', $member->id) }}" class="btn btn-outline-secondary">Back to Profile</a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.members.activation.store', $member->id) }}">
        @csrf
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><h2 class="h5 mb-0">Activation Settings</h2></div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label for="activation-plan" class="form-label">Membership Plan</label>
                    <select id="activation-plan" name="plan_id" class="form-select" required>
                        <option value="0" @selected((int) old('plan_id', $member->plan_id) === 0)>No Plan / Free</option>
                        @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" @selected((int) old('plan_id', $member->plan_id) === (int) $plan->id)>{{ $plan->plan_name }} — {{ $plan->duration_days }} days</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="activation-date" class="form-label">Plan Start Date</label>
                    <input id="activation-date" type="date" name="plan_activation_date" class="form-control" value="{{ old('plan_activation_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="activation-manager" class="form-label">Relationship Manager</label>
                    <select id="activation-manager" name="staff_id" class="form-select" required>
                        <option value="">Select staff</option>
                        @foreach($staff as $user)
                        <option value="{{ $user->id }}" @selected(old('staff_id') !== null ? (int) old('staff_id') === (int) $user->id : $member->relationship_manager === $user->name)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="activation-visibility" class="form-label">Profile Visibility</label>
                    <select id="activation-visibility" name="profile_hide" class="form-select" required>
                        <option value="No" @selected(old('profile_hide', strtolower((string) $member->profile_hide) === 'yes' ? 'Yes' : 'No') === 'No')>Visible</option>
                        <option value="Yes" @selected(old('profile_hide', strtolower((string) $member->profile_hide) === 'yes' ? 'Yes' : 'No') === 'Yes')>Hidden</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="activation-views" class="form-label">Profile View Count</label>
                    <input id="activation-views" type="number" min="0" max="2147483647" name="profile_view_count" class="form-control" value="{{ old('profile_view_count', $member->profile_view_count ?: 0) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="activation-wallet" class="form-label">Add to Wallet (₹)</label>
                    <input id="activation-wallet" type="number" min="0" max="1000000" step="0.01" name="wallet_amount" class="form-control" value="{{ old('wallet_amount', 0) }}" required>
                    <div class="form-text">Current balance: ₹{{ number_format((float) $walletBalance, 2) }}. Enter 0 to keep this balance.</div>
                </div>
            </div>
        </div>

        @php
            $defaultRanges = collect([[1, 20, 3], [21, 50, 8], [51, 100, 15], [101, 200, 25], [201, 1000, 50]])
                ->map(fn ($range) => ['range_from' => $range[0], 'range_to' => $range[1], 'price' => $range[2]])->all();
            $formRanges = old('ranges', $ranges->isNotEmpty() ? $ranges->map(fn ($range) => (array) $range)->all() : $defaultRanges);
        @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><h2 class="h5 mb-0">Profile View Rates</h2></div>
            <div class="card-body">
                <p class="text-muted">Set the price for each range of profile views. Ranges must not overlap.</p>
                <div id="activation-ranges">
                    @foreach($formRanges as $index => $range)
                    <div class="row g-2 mb-3" data-rate-row>
                        <div class="col"><label class="form-label">From<input aria-label="Range {{ $index + 1 }} start" class="form-control" type="number" min="1" name="ranges[{{ $index }}][range_from]" value="{{ $range['range_from'] ?? '' }}" required></label></div>
                        <div class="col"><label class="form-label">To<input aria-label="Range {{ $index + 1 }} end" class="form-control" type="number" min="1" name="ranges[{{ $index }}][range_to]" value="{{ $range['range_to'] ?? '' }}" required></label></div>
                        <div class="col"><label class="form-label">Price (₹)<input aria-label="Range {{ $index + 1 }} price" class="form-control" type="number" min="0" max="1000000" step="0.01" name="ranges[{{ $index }}][price]" value="{{ $range['price'] ?? '' }}" required></label></div>
                        <div class="col-auto align-self-end mb-2"><button type="button" class="btn btn-outline-danger" data-remove-rate aria-label="Remove range">×</button></div>
                    </div>
                    @endforeach
                </div>
                <button type="button" id="add-activation-range" class="btn btn-outline-secondary">Add Range</button>
            </div>
        </div>
        <button class="btn btn-success" type="submit"><i class="bi bi-person-check me-2"></i>Activate Member</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('activation-ranges');
    let nextIndex = {{ count($formRanges) }};
    document.getElementById('add-activation-range').addEventListener('click', () => {
        if (container.children.length >= 20) return;
        const row = container.querySelector('[data-rate-row]').cloneNode(true);
        row.querySelectorAll('input').forEach(input => {
            input.name = input.name.replace(/ranges\[\d+\]/, `ranges[${nextIndex}]`);
            input.value = '';
        });
        nextIndex++;
        container.appendChild(row);
    });
    container.addEventListener('click', event => {
        if (event.target.closest('[data-remove-rate]') && container.children.length > 1) {
            event.target.closest('[data-rate-row]').remove();
        }
    });
});
</script>
@endpush
