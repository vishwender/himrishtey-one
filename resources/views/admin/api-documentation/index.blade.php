@extends('admin.layout')

@section('title', 'API Documentation')
@section('page-title', 'API Documentation')

@push('styles')
<style>
    .api-endpoint { border-left: 4px solid var(--bs-border-color); }
    .api-method { min-width: 4.5rem; letter-spacing: .04em; }
    .api-uri { overflow-wrap: anywhere; }
    .api-code { background: var(--bs-tertiary-bg); border: 1px solid var(--bs-border-color); }
    .api-code pre { margin: 0; white-space: pre-wrap; overflow-wrap: anywhere; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">API Documentation</h1>
            <p class="text-muted mb-0">Live documentation generated from the application's registered API routes.</p>
        </div>
        <span class="badge text-bg-primary fs-6">{{ $endpointCount }} endpoints</span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Request setup</h2>
                    <div class="api-code rounded p-3 font-monospace small mb-3">{{ url('/api/v1') }}</div>
                    <p class="mb-2"><strong>Required on every request</strong></p>
                    <div class="api-code rounded p-3 font-monospace small">Accept: application/json<br>X-App-Code: himrishtey</div>
                    <p class="mb-2 mt-3"><strong>X-App-Code by application</strong></p>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Application</th>
                                    <th scope="col">Header value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Himrishtey</td><td><code>X-App-Code: himrishtey</code></td></tr>
                                <tr><td>Gallpakki</td><td><code>X-App-Code: gallpakki</code></td></tr>
                                <tr><td>Dogririshtey</td><td><code>X-App-Code: dogririshtey</code></td></tr>
                                <tr><td>Devbhoomi</td><td><code>X-App-Code: devbhoomi</code></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Authentication</h2>
                    <p class="mb-2">Protected endpoints require a Sanctum bearer token:</p>
                    <div class="api-code rounded p-3 font-monospace small">Authorization: Bearer &lt;token&gt;</div>
                    <p class="text-muted small mt-2 mb-0">{{ $authenticatedCount }} endpoints require authentication.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <label for="api-search" class="form-label fw-semibold">Search endpoints</label>
            <input id="api-search" class="form-control" type="search" placeholder="Search by path, method, feature, or controller…">
        </div>
    </div>

    <div id="api-groups">
        @foreach($endpointGroups as $group => $endpoints)
        <section class="api-group mb-4" data-group="{{ Str::lower($group) }}">
            <div class="d-flex align-items-center gap-2 mb-3">
                <h2 class="h4 mb-0">{{ $group }}</h2>
                <span class="badge rounded-pill text-bg-secondary">{{ $endpoints->count() }}</span>
            </div>

            <div class="d-grid gap-3">
                @foreach($endpoints as $endpoint)
                <article class="api-endpoint card border-0 shadow-sm" data-search="{{ Str::lower($group.' '.$endpoint['methods']->join(' ').' '.$endpoint['uri'].' '.$endpoint['action'].' '.($endpoint['details']['description'] ?? '')) }}">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            @foreach($endpoint['methods'] as $method)
                            <span class="api-method badge text-bg-{{ match($method) { 'GET' => 'success', 'POST' => 'primary', 'PUT', 'PATCH' => 'warning', 'DELETE' => 'danger', default => 'secondary' } }}">{{ $method }}</span>
                            @endforeach
                            <code class="api-uri fs-6">{{ $endpoint['uri'] }}</code>
                            @if($endpoint['authenticated'])
                            <span class="badge text-bg-dark"><i class="bi bi-lock-fill me-1"></i>Bearer token</span>
                            @else
                            <span class="badge text-bg-light border text-dark">Public</span>
                            @endif
                        </div>

                        <div class="text-muted small">Handler: <code>{{ $endpoint['action'] }}</code></div>
                        @if($endpoint['parameters']->isNotEmpty())
                        <div class="text-muted small mt-1">Path parameters: {{ $endpoint['parameters']->join(', ') }}</div>
                        @endif

                        @if($endpoint['details'])
                        <div class="mt-3 pt-3 border-top">
                            <p>{{ $endpoint['details']['description'] }}</p>

                            <div class="row g-3">
                                <div class="col-xl-6">
                                    <h3 class="h6">{{ $endpoint['details']['request_label'] ?? 'JSON request' }}</h3>
                                    <div class="api-code rounded p-3 small">
                                        <pre><code>{{ json_encode($endpoint['details']['request'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <h3 class="h6">Successful response</h3>
                                    <div class="api-code rounded p-3 small">
                                        <pre><code>{{ json_encode($endpoint['details']['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                    </div>
                                </div>
                            </div>

                            <h3 class="h6 mt-3">Important behavior</h3>
                            <ul class="small text-muted mb-0">
                                @foreach($endpoint['details']['notes'] as $note)
                                <li>{{ $note }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </article>
                @endforeach
            </div>
        </section>
        @endforeach
    </div>

    <div id="api-no-results" class="alert alert-info d-none">No endpoints match your search.</div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('api-search').addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        let visibleCount = 0;

        document.querySelectorAll('.api-group').forEach((group) => {
            let groupCount = 0;

            group.querySelectorAll('.api-endpoint').forEach((endpoint) => {
                const visible = endpoint.dataset.search.includes(query);
                endpoint.classList.toggle('d-none', !visible);
                groupCount += visible ? 1 : 0;
            });

            group.classList.toggle('d-none', groupCount === 0);
            visibleCount += groupCount;
        });

        document.getElementById('api-no-results').classList.toggle('d-none', visibleCount !== 0);
    });
</script>
@endpush
