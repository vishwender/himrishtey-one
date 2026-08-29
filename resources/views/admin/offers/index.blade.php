@extends('admin.layout')

@section('title', 'Offers')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h1 class="h3 mb-1">
                Offers
            </h1>

            <p class="text-muted mb-0">
                Manage promotional offers for the selected site.
            </p>

        </div>


        <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addOfferModal">

            <i class="bi bi-plus-lg me-1"></i>
            Add Offer

        </button>

    </div>


    {{-- Flash --}}
    @if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    @endif


    {{-- Errors --}}
    @if($errors->any())

    <div class="alert alert-danger">

        <ul class="mb-0">

            @foreach($errors->all() as $error)

            <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

    @endif


    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('admin.offers.index') }}">

                <div class="row g-3 align-items-end">

                    <div class="col-md-6">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Search title or description">

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option value="">
                                All Statuses
                            </option>

                            <option
                                value="Active"
                                @selected(request('status')==='Active' )>

                                Active

                            </option>

                            <option
                                value="Inactive"
                                @selected(request('status')==='Inactive' )>

                                Inactive

                            </option>

                        </select>

                    </div>


                    <div class="col-md-3">

                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-search me-1"></i>
                            Search

                        </button>

                        <a
                            href="{{ route('admin.offers.index') }}"
                            class="btn btn-light">

                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Table --}}
    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th>
                            Image
                        </th>

                        <th>
                            Offer
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Time
                        </th>

                        <th>
                            Status
                        </th>

                        <th class="text-end">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($offers as $offer)

                    <tr>

                        {{-- Image --}}
                        <td style="width:100px;">

                            @if($offer->image)

                            <img
                                src="{{ asset(
                                            'images/offers/' .
                                            $offer->image
                                        ) }}"
                                alt="{{ $offer->title }}"
                                class="rounded border"
                                style="
                                            width:80px;
                                            height:60px;
                                            object-fit:cover;
                                        ">

                            @else

                            <div
                                class="bg-light border rounded d-flex align-items-center justify-content-center"
                                style="
                                            width:80px;
                                            height:60px;
                                        ">

                                <i class="bi bi-image text-muted"></i>

                            </div>

                            @endif

                        </td>


                        {{-- Offer --}}
                        <td>

                            <div class="fw-semibold mb-1">

                                {{ $offer->title }}

                            </div>

                            <div class="small text-muted">

                                {{ \Illuminate\Support\Str::limit(
                                        $offer->description,
                                        100
                                    ) }}

                            </div>

                        </td>


                        {{-- Date --}}
                        <td>

                            {{ $offer->offer_date }}

                        </td>


                        {{-- Time --}}
                        <td>

                            {{ $offer->offer_time }}

                        </td>


                        {{-- Status --}}
                        <td>

                            @if($offer->status === 'Active')

                            <span class="badge bg-success-subtle text-success-emphasis">

                                Active

                            </span>

                            @else

                            <span class="badge bg-secondary-subtle text-secondary-emphasis">

                                Inactive

                            </span>

                            @endif

                        </td>


                        {{-- Actions --}}
                        <td class="text-end">

                            <div class="dropdown">

                                <button
                                    class="btn btn-sm btn-light"
                                    type="button"
                                    data-bs-toggle="dropdown">

                                    <i class="bi bi-three-dots-vertical"></i>

                                </button>


                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li>

                                        <button
                                            type="button"
                                            class="dropdown-item edit-offer-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editOfferModal"
                                            data-id="{{ $offer->id }}"
                                            data-title="{{ $offer->title }}"
                                            data-description="{{ $offer->description }}"
                                            data-date="{{ $offer->offer_date }}"
                                            data-time="{{ $offer->offer_time }}"
                                            data-status="{{ $offer->status }}">

                                            <i class="bi bi-pencil me-2"></i>

                                            Edit

                                        </button>

                                    </li>


                                    <li>

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                    'admin.offers.toggle-status',
                                                    $offer->id
                                                ) }}">

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="dropdown-item">

                                                <i class="bi bi-toggle-on me-2"></i>

                                                {{
                                                        $offer->status === 'Active'
                                                            ? 'Deactivate'
                                                            : 'Activate'
                                                    }}

                                            </button>

                                        </form>

                                    </li>


                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>


                                    <li>

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                    'admin.offers.destroy',
                                                    $offer->id
                                                ) }}"
                                            onsubmit="
                                                    return confirm(
                                                        'Delete this offer?'
                                                    );
                                                ">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="dropdown-item text-danger">

                                                <i class="bi bi-trash me-2"></i>

                                                Delete

                                            </button>

                                        </form>

                                    </li>

                                </ul>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="6"
                            class="text-center py-5">

                            <i class="bi bi-tags fs-2 text-muted d-block mb-2"></i>

                            <div class="text-muted">
                                No offers found.
                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($offers->hasPages())

        <div class="card-footer bg-white">

            {{ $offers->links() }}

        </div>

        @endif

    </div>

</div>


{{-- ================================================================
    ADD OFFER
================================================================= --}}

<div
    class="modal fade"
    id="addOfferModal"
    tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('admin.offers.store') }}"
                enctype="multipart/form-data">

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title fw-bold">
                        Add Offer
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-12">

                            <label class="form-label">
                                Offer Title
                            </label>

                            <input
                                type="text"
                                name="title"
                                class="form-control"
                                required>

                        </div>


                        <div class="col-12">

                            <label class="form-label">
                                Description
                            </label>

                            <textarea
                                name="description"
                                class="form-control"
                                rows="4"
                                required></textarea>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Offer Date
                            </label>

                            <input
                                type="date"
                                name="offer_date"
                                class="form-control"
                                required>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Offer Time
                            </label>

                            <input
                                type="time"
                                name="offer_time"
                                class="form-control"
                                required>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Status
                            </label>

                            <select
                                name="status"
                                class="form-select"
                                required>

                                <option value="Active">
                                    Active
                                </option>

                                <option value="Inactive">
                                    Inactive
                                </option>

                            </select>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Offer Image
                            </label>

                            <input
                                type="file"
                                name="image"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.webp"
                                required>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Save Offer

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- ================================================================
    EDIT OFFER
================================================================= --}}

<div
    class="modal fade"
    id="editOfferModal"
    tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form
                id="editOfferForm"
                method="POST"
                enctype="multipart/form-data">

                @csrf
                @method('PUT')


                <div class="modal-header">

                    <h5 class="modal-title fw-bold">
                        Edit Offer
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-12">

                            <label class="form-label">
                                Offer Title
                            </label>

                            <input
                                type="text"
                                id="editOfferTitle"
                                name="title"
                                class="form-control"
                                required>

                        </div>


                        <div class="col-12">

                            <label class="form-label">
                                Description
                            </label>

                            <textarea
                                id="editOfferDescription"
                                name="description"
                                class="form-control"
                                rows="4"
                                required></textarea>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Offer Date
                            </label>

                            <input
                                type="date"
                                id="editOfferDate"
                                name="offer_date"
                                class="form-control"
                                required>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Offer Time
                            </label>

                            <input
                                type="time"
                                id="editOfferTime"
                                name="offer_time"
                                class="form-control"
                                required>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Status
                            </label>

                            <select
                                id="editOfferStatus"
                                name="status"
                                class="form-select">

                                <option value="Active">
                                    Active
                                </option>

                                <option value="Inactive">
                                    Inactive
                                </option>

                            </select>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Replace Image
                            </label>

                            <input
                                type="file"
                                name="image"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.webp">

                            <small class="text-muted">
                                Leave empty to keep the existing image.
                            </small>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Update Offer

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const buttons =
            document.querySelectorAll('.edit-offer-btn');

        const form =
            document.getElementById('editOfferForm');

        buttons.forEach(function(button) {

            button.addEventListener('click', function() {

                const id =
                    this.dataset.id;

                document.getElementById('editOfferTitle').value =
                    this.dataset.title;

                document.getElementById('editOfferDescription').value =
                    this.dataset.description;

                document.getElementById('editOfferDate').value =
                    this.dataset.date;

                document.getElementById('editOfferTime').value =
                    this.dataset.time;

                document.getElementById('editOfferStatus').value =
                    this.dataset.status;

                form.action =
                    "{{ url('/admin/offers') }}/" + id;

            });

        });

    });
</script>

@endsection