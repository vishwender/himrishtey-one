@extends('admin.layout')

@section('title', 'Site Stats')

@push('styles')
<style>
    .stats-kpi { border: 0; box-shadow: 0 .25rem 1rem rgba(21, 29, 56, .07); }
    .stats-kpi .icon { width: 42px; height: 42px; display: grid; place-items: center; border-radius: 12px; background: var(--bs-primary-bg-subtle); color: var(--bs-primary); font-size: 1.2rem; }
    .stats-kpi h3 { font-family: Outfit, sans-serif; }
    .stats-chart { position: relative; min-height: 310px; }
    .stats-chart canvas { max-height: 310px; }
    .stats-section-title { font-size: 1.05rem; font-weight: 700; }
</style>
@endpush

@section('content')
@php($site = app(\App\Services\SiteManager::class)->current())

<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Site Stats</h1>
            <p class="text-muted mb-0">Analytics for {{ $site?->name ?? 'the selected site' }}.</p>
        </div>
        <form method="GET" class="d-flex align-items-center gap-2">
            <label for="months" class="text-muted text-nowrap">Reporting period</label>
            <select id="months" name="months" class="form-select" onchange="this.form.submit()">
                @foreach([3, 6, 12, 24] as $option)
                    <option value="{{ $option }}" @selected($months === $option)>Last {{ $option }} months</option>
                @endforeach
            </select>
        </form>
    </div>

    <h2 class="stats-section-title mb-3">Core member stats</h2>
    <div class="row g-3 mb-4">
        @foreach([
            ['Total members', $stats['core']['total'], 'bi-people'],
            ['Active', $stats['core']['active'], 'bi-person-check'],
            ['Inactive', $stats['core']['inactive'], 'bi-person-dash'],
            ['Trusted', $stats['core']['trusted'], 'bi-patch-check'],
            ['Promoted', $stats['core']['promoted'], 'bi-megaphone'],
            ['Hidden', $stats['core']['hidden'], 'bi-eye-slash'],
            ['Site Hits', $stats['site_hits'], 'bi-cursor'],
        ] as [$label, $value, $icon])
        <div class="col-sm-6 col-lg-4 col-xl">
            <div class="card stats-kpi h-100"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start"><div><div class="text-muted small">{{ $label }}</div><h3 class="mb-0 mt-2">{{ number_format($value) }}</h3></div><span class="icon"><i class="bi {{ $icon }}"></i></span></div>
            </div></div>
        </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4"><div class="card h-100"><div class="card-header bg-transparent"><strong>Gender distribution</strong></div><div class="card-body stats-chart"><canvas id="genderChart"></canvas></div></div></div>
        <div class="col-xl-4"><div class="card h-100"><div class="card-header bg-transparent"><strong>Marital status</strong></div><div class="card-body stats-chart"><canvas id="maritalChart"></canvas></div></div></div>
        <div class="col-xl-4"><div class="card h-100"><div class="card-header bg-transparent"><strong>Religion demographics</strong></div><div class="card-body stats-chart"><canvas id="religionChart"></canvas></div></div></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-7"><div class="card h-100"><div class="card-header bg-transparent"><strong>Registration trends</strong></div><div class="card-body stats-chart"><canvas id="registrationChart"></canvas></div></div></div>
        <div class="col-xl-5"><div class="card h-100"><div class="card-header bg-transparent"><strong>Profile quality</strong></div><div class="card-body stats-chart"><canvas id="qualityChart"></canvas></div></div></div>
    </div>

    <h2 class="stats-section-title mb-3">Activity stats</h2>
    <div class="row g-3 mb-4">
        @foreach([
            ['Interests sent', $stats['activity']['sent_interests'], 'bi-heart'],
            ['Profile views', $stats['activity']['profile_views'], 'bi-eye'],
            ['Contact views', $stats['activity']['contact_views'], 'bi-person-lines-fill'],
            ['Activity events', $stats['activity']['activity_events'], 'bi-activity'],
        ] as [$label, $value, $icon])
        <div class="col-sm-6 col-xl-3"><div class="card stats-kpi h-100"><div class="card-body d-flex justify-content-between"><div><div class="text-muted small">{{ $label }}</div><h3 class="mb-0 mt-2">{{ number_format($value) }}</h3></div><span class="icon"><i class="bi {{ $icon }}"></i></span></div></div></div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-6"><div class="card h-100"><div class="card-header bg-transparent"><strong>Membership stats</strong></div><div class="card-body"><div class="row g-3 mb-3"><div class="col-6"><div class="p-3 rounded bg-body-tertiary"><small class="text-muted">Paid members</small><h3>{{ number_format($stats['membership']['paid']) }}</h3></div></div><div class="col-6"><div class="p-3 rounded bg-body-tertiary"><small class="text-muted">Free members</small><h3>{{ number_format($stats['membership']['free']) }}</h3></div></div></div><div class="stats-chart"><canvas id="membershipChart"></canvas></div></div></div></div>
        <div class="col-xl-6"><div class="card h-100"><div class="card-header bg-transparent"><strong>Wallet and revenue</strong></div><div class="card-body"><div class="row g-3">
            @foreach([['Payments', $stats['finance']['paymentCount'], false], ['Revenue', $stats['finance']['revenue'], true], ['Wallet balance', $stats['finance']['walletBalance'], true], ['Wallet credits', $stats['finance']['walletAdded'], true]] as [$label, $value, $currency])
            <div class="col-6"><div class="p-3 rounded bg-body-tertiary"><small class="text-muted">{{ $label }}</small><h3 class="mb-0">{{ $currency ? '₹'.number_format($value, 2) : number_format($value) }}</h3></div></div>
            @endforeach
        </div></div></div></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-5"><div class="card h-100"><div class="card-header bg-transparent"><strong>Rotations and staff workflow</strong></div><div class="card-body"><div class="row g-3">
            @foreach(['Pending' => 'pending', 'Completed' => 'completed', 'Cancelled' => 'cancelled', 'Overdue' => 'overdue'] as $label => $key)
            <div class="col-6"><div class="p-3 border rounded"><small class="text-muted">{{ $label }} rotations</small><h4 class="mb-0">{{ number_format($stats['workflow']['rotations'][$key]) }}</h4></div></div>
            @endforeach
            <div class="col-12"><div class="alert alert-primary mb-0"><i class="bi bi-person-workspace me-2"></i><strong>{{ number_format($stats['workflow']['staff_actions']) }}</strong> staff actions in this period</div></div>
        </div></div></div></div>
        <div class="col-xl-7"><div class="card h-100"><div class="card-header bg-transparent"><strong>Location analytics — top states</strong></div><div class="card-body stats-chart"><canvas id="locationChart"></canvas></div></div></div>
    </div>

    <div class="card">
        <div class="card-header bg-transparent"><strong>Top cities</strong></div>
        <div class="card-body">
            @forelse($stats['locations']['cities'] as $label => $total)
                <div class="d-flex justify-content-between border-bottom py-2"><span>{{ $label }}</span><strong>{{ number_format($total) }}</strong></div>
            @empty
                <p class="text-muted mb-0">No city data available.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const palette = ['#6f42c1','#0d6efd','#20c997','#ffc107','#dc3545','#0dcaf0','#fd7e14','#6c757d','#198754','#d63384'];
    const values = @json($stats);
    const makeChart = (id, type, labels, data, label, options = {}) => {
        const canvas = document.getElementById(id);
        if (!canvas) return;
        new Chart(canvas, {type, data: {labels, datasets: [{label, data, backgroundColor: type === 'line' ? 'rgba(111,66,193,.15)' : palette, borderColor: type === 'line' ? '#6f42c1' : palette, borderWidth: 2, fill: type === 'line', tension: .3}]}, options: {responsive: true, maintainAspectRatio: false, plugins: {legend: {display: ['doughnut','pie'].includes(type), position: 'bottom'}}, scales: ['doughnut','pie'].includes(type) ? {} : {y: {beginAtZero: true, ticks: {precision: 0}}}, ...options}});
    };
    makeChart('genderChart', 'doughnut', Object.keys(values.gender), Object.values(values.gender), 'Members');
    makeChart('maritalChart', 'doughnut', Object.keys(values.demographics.marital_status), Object.values(values.demographics.marital_status), 'Members');
    makeChart('religionChart', 'bar', Object.keys(values.demographics.religion), Object.values(values.demographics.religion), 'Members', {indexAxis: 'y'});
    makeChart('registrationChart', 'line', values.registrations.labels, values.registrations.values, 'Registrations');
    makeChart('qualityChart', 'bar', ['With photo','Photo approved','Trusted','80%+ complete','Needs attention'], [values.quality.with_photo, values.quality.approved_photo, values.quality.trusted, values.quality.complete, values.quality.needs_attention], 'Profiles');
    makeChart('membershipChart', 'bar', Object.keys(values.membership.plans), Object.values(values.membership.plans), 'Members');
    makeChart('locationChart', 'bar', Object.keys(values.locations.states), Object.values(values.locations.states), 'Members', {indexAxis: 'y'});

});
</script>
@endpush
