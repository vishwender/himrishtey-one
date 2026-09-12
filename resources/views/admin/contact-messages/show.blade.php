@extends('admin.layout')
@section('title', 'Contact Message')
@section('page-title', 'Contact Message')
@section('content')
<a href="{{ route('admin.contact-messages.index') }}" class="btn btn-outline-secondary mb-3">Back to inbox</a>
<div class="card shadow-sm"><div class="card-body">
    <h4>{{ $message->subject }}</h4>
    <dl class="row mt-3">
        <dt class="col-sm-2">From</dt><dd class="col-sm-10">{{ $message->name }}</dd>
        <dt class="col-sm-2">Email</dt><dd class="col-sm-10">{{ $message->email }}</dd>
        <dt class="col-sm-2">Phone</dt><dd class="col-sm-10">{{ $message->phone ?: 'Not provided' }}</dd>
        <dt class="col-sm-2">Profile ID</dt><dd class="col-sm-10">{{ $message->profile_id ?: 'Not provided' }}</dd>
        <dt class="col-sm-2">Received</dt><dd class="col-sm-10">{{ $message->created_at->format('d M Y, h:i A') }}</dd>
    </dl>
    <hr><div style="white-space: pre-wrap; overflow-wrap: anywhere;">{{ $message->message }}</div>
    <p id="read-error" class="text-danger mt-3" hidden>Could not mark this message as read. Refresh to try again.</p>
</div></div>
@endsection
@push('scripts')
@if(!$message->read_at)
<script>
fetch(@json(route('admin.contact-messages.read', $message->id)), {
    method: 'PATCH', headers: {'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json'}
}).then(response => {
    if (!response.ok) throw new Error('Unable to mark read');
    window.dispatchEvent(new Event('contact-message-read'));
}).catch(() => document.getElementById('read-error').hidden = false);
</script>
@endif
@endpush
