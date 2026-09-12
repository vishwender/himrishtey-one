@extends('admin.layout')
@section('title', 'Contact Messages')
@section('page-title', 'Contact Messages')
@section('content')
<div class="card shadow-sm">
    <div class="card-header"><h5 class="mb-1">Contact Us inbox</h5><p class="text-muted mb-0">Messages received for the selected site.</p></div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-7"><label for="search" class="form-label">Search</label><input id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, email or subject"></div>
            <div class="col-md-3"><label for="status" class="form-label">Status</label><select id="status" name="status" class="form-select">@foreach(['all' => 'All messages', 'unread' => 'Unread', 'read' => 'Read'] as $value => $label)<option value="{{ $value }}" @selected(request('status', 'all') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary">Filter</button></div>
        </form>
        <div class="table-responsive"><table class="table align-middle">
            <thead><tr><th>Status</th><th>Sender</th><th>Subject</th><th>Received</th><th></th></tr></thead>
            <tbody>@forelse($messages as $message)
            <tr class="{{ $message->read_at ? '' : 'fw-semibold' }}">
                <td><span class="badge {{ $message->read_at ? 'bg-secondary' : 'bg-primary' }}">{{ $message->read_at ? 'Read' : 'New' }}</span></td>
                <td>{{ $message->name }}<div class="small text-muted">{{ $message->email }}</div></td>
                <td>{{ $message->subject }}</td><td>{{ $message->created_at->format('d M Y, h:i A') }}</td>
                <td><a href="{{ route('admin.contact-messages.show', $message->id) }}" class="btn btn-sm btn-outline-primary">View message</a></td>
            </tr>
            @empty<tr><td colspan="5" class="text-center text-muted py-5">No contact messages found.</td></tr>@endforelse</tbody>
        </table></div>
        {{ $messages->links() }}
    </div>
</div>
@endsection
