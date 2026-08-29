@extends('admin.layout')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
        Member Header
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex align-items-center">

                {{-- Profile Photo --}}
                @if(!empty($member->photo))

                <img
                    src="{{ asset('storage/' . $member->photo) }}"
                    width="70"
                    height="70"
                    class="rounded-circle object-fit-cover me-3"
                    alt="{{ $member->full_name }}">

                @else

                <div
                    class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3"
                    style="width:70px;height:70px;">

                    <i class="bi bi-person fs-3 text-muted"></i>

                </div>

                @endif


                {{-- Member Information --}}
                <div>

                    <h4 class="mb-1">
                        {{ $member->full_name }}
                    </h4>

                    <div class="text-muted">

                        Profile ID:
                        <strong>
                            {{ $member->profile_id }}
                        </strong>

                        @if(!empty($member->email))

                        <span class="mx-2">|</span>

                        {{ $member->email }}

                        @endif

                    </div>

                </div>

            </div>

        </div>
    </div>


    {{-- =========================================================
        Activity Navigation
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex flex-wrap gap-2">

                {{-- Shortlisted --}}
                <a
                    href="{{ route('admin.activities.member', [
                        'memberId' => $member->id,
                        'activity' => 'shortlisted'
                    ]) }}"
                    class="btn {{ $activity === 'shortlisted' ? 'btn-primary' : 'btn-outline-primary' }}">

                    <i class="bi bi-bookmark me-1"></i>
                    Shortlisted

                </a>


                {{-- Sent Interests --}}
                <a
                    href="{{ route('admin.activities.member', [
                        'memberId' => $member->id,
                        'activity' => 'sent-interests'
                    ]) }}"
                    class="btn {{ $activity === 'sent-interests' ? 'btn-primary' : 'btn-outline-primary' }}">

                    <i class="bi bi-send me-1"></i>
                    Sent Interests

                </a>


                {{-- Received Interests --}}
                <a
                    href="{{ route('admin.activities.member', [
                        'memberId' => $member->id,
                        'activity' => 'received-interests'
                    ]) }}"
                    class="btn {{ $activity === 'received-interests' ? 'btn-primary' : 'btn-outline-primary' }}">

                    <i class="bi bi-inbox me-1"></i>
                    Received Interests

                </a>


                {{-- Profile Views --}}
                <a
                    href="{{ route('admin.activities.member', [
                        'memberId' => $member->id,
                        'activity' => 'profile-views'
                    ]) }}"
                    class="btn {{ $activity === 'profile-views' ? 'btn-primary' : 'btn-outline-primary' }}">

                    <i class="bi bi-eye me-1"></i>
                    Profile Views

                </a>


                {{-- Contact Views --}}
                <a
                    href="{{ route('admin.activities.member', [
                        'memberId' => $member->id,
                        'activity' => 'contact-views'
                    ]) }}"
                    class="btn {{ $activity === 'contact-views' ? 'btn-primary' : 'btn-outline-primary' }}">

                    <i class="bi bi-person-lines-fill me-1"></i>
                    Contact Views

                </a>


                {{-- Wallet Payments --}}
                <a
                    href="{{ route('admin.activities.member', [
                        'memberId' => $member->id,
                        'activity' => 'wallet-payments'
                    ]) }}"
                    class="btn {{ $activity === 'wallet-payments' ? 'btn-primary' : 'btn-outline-primary' }}">

                    <i class="bi bi-wallet2 me-1"></i>
                    Wallet Payments

                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
        Activity Data
    ========================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Activity Title --}}
            <h5 class="mb-4">

                @switch($activity)

                @case('shortlisted')

                <i class="bi bi-bookmark me-2"></i>
                Shortlisted Profiles

                @break


                @case('sent-interests')

                <i class="bi bi-send me-2"></i>
                Sent Interests

                @break


                @case('received-interests')

                <i class="bi bi-inbox me-2"></i>
                Received Interests

                @break


                @case('profile-views')

                <i class="bi bi-eye me-2"></i>
                Profile Views

                @break


                @case('contact-views')

                <i class="bi bi-person-lines-fill me-2"></i>
                Contact Views

                @break


                @case('wallet-payments')

                <i class="bi bi-wallet2 me-2"></i>
                Wallet Payments

                @break

                @endswitch

            </h5>


            {{-- =================================================
                Data Exists
            ================================================== --}}

            @if($data->count())

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        {{-- =================================================
                                Shortlisted
                            ================================================== --}}

                        @if($activity === 'shortlisted')

                        <tr>
                            <th>#</th>
                            <th>Profile</th>
                            <th>Profile ID</th>
                            <th>Date</th>
                        </tr>


                        {{-- =================================================
                                Sent Interests
                            ================================================== --}}

                        @elseif($activity === 'sent-interests')

                        <tr>
                            <th>#</th>
                            <th>Profile</th>
                            <th>Profile ID</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>


                        {{-- =================================================
                                Received Interests
                            ================================================== --}}

                        @elseif($activity === 'received-interests')

                        <tr>
                            <th>#</th>
                            <th>Profile</th>
                            <th>Profile ID</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>


                        {{-- =================================================
                                Profile Views
                            ================================================== --}}

                        @elseif($activity === 'profile-views')

                        <tr>
                            <th>#</th>
                            <th>Viewed Profile</th>
                            <th>Profile ID</th>
                            <th>Date</th>
                        </tr>


                        {{-- =================================================
                                Contact Views
                            ================================================== --}}

                        @elseif($activity === 'contact-views')

                        <tr>
                            <th>#</th>
                            <th>Viewed Profile</th>
                            <th>Profile ID</th>
                        </tr>


                        {{-- =================================================
                                Wallet Payments
                            ================================================== --}}

                        @elseif($activity === 'wallet-payments')

                        <tr>
                            <th>#</th>
                            <th>Payment ID</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                            <th>Date</th>
                        </tr>

                        @endif

                    </thead>


                    <tbody>

                        @foreach($data as $row)

                        {{-- =================================================
                                    SHORTLISTED
                        ================================================== --}}

                        @if($activity === 'shortlisted')

                        <tr>

                            <td>
                                {{ $row->id }}
                            </td>

                            <td>
                                <strong>
                                    {{ $row->target_full_name ?? 'Unknown Member' }}
                                </strong>
                            </td>

                            <td>
                                {{ $row->target_profile_id ?? 'N/A' }}
                            </td>

                            <td>
                                {{ $row->created_at }}
                            </td>

                        </tr>


                        {{-- =================================================
                                    SENT INTERESTS
                         ================================================== --}}

                        @elseif($activity === 'sent-interests')

                        <tr>

                            <td>
                                {{ $row->id }}
                            </td>

                            <td>
                                <strong>
                                    {{ $row->target_full_name ?? 'Unknown Member' }}
                                </strong>
                            </td>

                            <td>
                                {{ $row->target_profile_id ?? 'N/A' }}
                            </td>

                            <td>

                                @if((string) $row->status === '1')

                                <span class="badge bg-success">
                                    Accepted
                                </span>

                                @elseif((string) $row->status === '2')

                                <span class="badge bg-danger">
                                    Rejected
                                </span>

                                @else

                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>

                                @endif

                            </td>

                            <td>
                                {{ $row->created_at }}
                            </td>

                        </tr>


                        {{-- =================================================
                                    RECEIVED INTERESTS
                        ================================================== --}}

                        @elseif($activity === 'received-interests')

                        <tr>

                            <td>
                                {{ $row->id }}
                            </td>

                            <td>
                                <strong>
                                    {{ $row->sender_full_name ?? 'Unknown Member' }}
                                </strong>
                            </td>

                            <td>
                                {{ $row->sender_profile_id ?? 'N/A' }}
                            </td>

                            <td>

                                @if((string) $row->status === '1')

                                <span class="badge bg-success">
                                    Accepted
                                </span>

                                @elseif((string) $row->status === '2')

                                <span class="badge bg-danger">
                                    Rejected
                                </span>

                                @else

                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>

                                @endif

                            </td>

                            <td>
                                {{ $row->created_at }}
                            </td>

                        </tr>


                        {{-- =================================================
                                    PROFILE VIEWS
                                ================================================== --}}

                        @elseif($activity === 'profile-views')

                        <tr>

                            <td>
                                {{ $row->id }}
                            </td>

                            <td>

                                <strong>
                                    {{ $row->viewed_full_name ?? 'Unknown Member' }}
                                </strong>

                            </td>

                            <td>
                                {{ $row->viewed_profile_id_display ?? 'N/A' }}
                            </td>

                            <td>
                                {{ $row->created_at }}
                            </td>

                        </tr>


                        {{-- =================================================
                                    CONTACT VIEWS
                                ================================================== --}}

                        @elseif($activity === 'contact-views')

                        <tr>

                            <td>
                                {{ $row->id }}
                            </td>

                            <td>

                                <strong>
                                    {{ $row->viewed_full_name ?? 'Unknown Member' }}
                                </strong>

                            </td>

                            <td>
                                {{ $row->viewed_profile_id_display ?? 'N/A' }}
                            </td>

                        </tr>


                        {{-- =================================================
                                    WALLET PAYMENTS
                                ================================================== --}}

                        @elseif($activity === 'wallet-payments')

                        <tr>

                            <td>
                                {{ $row->id }}
                            </td>

                            <td>
                                {{ $row->payment_id }}
                            </td>

                            <td>
                                ₹{{ number_format((float) $row->amount, 2) }}
                            </td>

                            <td>
                                {{ $row->remarks ?: '—' }}
                            </td>

                            <td>
                                {{ $row->payment_date }}
                            </td>

                        </tr>

                        @endif

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- =================================================
                    Pagination
                ================================================== --}}

            <div class="mt-4">

                {{ $data->links() }}

            </div>


            @else

            {{-- =================================================
                    Empty State
                ================================================== --}}

            <div class="text-center py-5">

                <div class="mb-3">

                    <i class="bi bi-inbox fs-1 text-muted"></i>

                </div>

                <h6 class="text-muted">
                    No activity found
                </h6>

                <p class="text-muted mb-0">
                    There is no recorded activity for this member.
                </p>

            </div>

            @endif

        </div>

    </div>

</div>

@endsection