@extends('admin.layout')

@section('title', 'Wallet Offers')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h1 class="h3 mb-1">
                Wallet Offers
            </h1>

            <p class="text-muted mb-0">
                Manage wallet recharge offers for the selected site.
            </p>

        </div>


        <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addWalletOfferModal">

            <i class="bi bi-plus-lg me-1"></i>

            Add Wallet Offer

        </button>

    </div>


    {{-- Success --}}
    @if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        <i class="bi bi-check-circle me-2"></i>

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


    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form
                action="{{ route('admin.wallet-offers.index') }}"
                method="GET">

                <div class="row g-3 align-items-end">

                    <div class="col-md-8">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Search offer title or description">

                    </div>


                    <div class="col-md-4">

                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-search me-1"></i>
                            Search

                        </button>

                        <a
                            href="{{ route('admin.wallet-offers.index') }}"
                            class="btn btn-light">

                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Wallet Offers --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="fw-bold mb-0">
                    Wallet Offers
                </h5>

                <span class="small text-muted">
                    {{ number_format($walletOffers->total()) }}
                    offer(s)
                </span>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Offer
                        </th>

                        <th>
                            Pay Amount
                        </th>

                        <th>
                            Bonus
                        </th>

                        <th>
                            Bonus Amount
                        </th>

                        <th>
                            Wallet Credit
                        </th>

                        <th class="text-end">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($walletOffers as $offer)

                    @php

                    $amount =
                    (float) $offer->amount;

                    $percentage =
                    (float) $offer->add_on_percentage;

                    $finalAmount =
                    (float) $offer->final_amount;

                    $bonusAmount =
                    $finalAmount - $amount;

                    @endphp


                    <tr>

                        <td>
                            {{ $offer->id }}
                        </td>


                        {{-- Offer --}}
                        <td style="min-width:240px;">

                            <div class="fw-semibold">
                                {{ $offer->title }}
                            </div>

                            <div class="small text-muted mt-1">
                                {{ \Illuminate\Support\Str::limit(
                                        $offer->description,
                                        90
                                    ) }}
                            </div>

                        </td>


                        {{-- Amount --}}
                        <td>

                            <span class="fw-semibold">

                                ₹{{ number_format(
                                        $amount,
                                        2
                                    ) }}

                            </span>

                        </td>


                        {{-- Percentage --}}
                        <td>

                            @if($percentage > 0)

                            <span class="badge bg-success-subtle text-success-emphasis">

                                +{{ number_format(
                                            $percentage,
                                            2
                                        ) }}%

                            </span>

                            @else

                            <span class="badge bg-light text-dark border">
                                No Bonus
                            </span>

                            @endif

                        </td>


                        {{-- Bonus Amount --}}
                        <td>

                            @if($bonusAmount > 0)

                            <span class="text-success fw-semibold">

                                +₹{{ number_format(
                                            $bonusAmount,
                                            2
                                        ) }}

                            </span>

                            @else

                            —

                            @endif

                        </td>


                        {{-- Final Amount --}}
                        <td>

                            <div class="fw-bold text-primary">

                                ₹{{ number_format(
                                        $finalAmount,
                                        2
                                    ) }}

                            </div>

                            <small class="text-muted">
                                Wallet Credit
                            </small>

                        </td>


                        {{-- Actions --}}
                        <td class="text-end">

                            <div class="dropdown">

                                <button
                                    type="button"
                                    class="btn btn-sm btn-light"
                                    data-bs-toggle="dropdown">

                                    <i class="bi bi-three-dots-vertical"></i>

                                </button>


                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li>

                                        <button
                                            type="button"
                                            class="dropdown-item edit-wallet-offer-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editWalletOfferModal"

                                            data-id="{{ $offer->id }}"

                                            data-title="{{ $offer->title }}"

                                            data-amount="{{ $offer->amount }}"

                                            data-percentage="{{ $offer->add_on_percentage }}"

                                            data-description="{{ $offer->description }}">

                                            <i class="bi bi-pencil me-2"></i>

                                            Edit

                                        </button>

                                    </li>


                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>


                                    <li>

                                        <form
                                            action="{{ route(
                                                    'admin.wallet-offers.destroy',
                                                    $offer->id
                                                ) }}"
                                            method="POST"
                                            onsubmit="
                                                    return confirm(
                                                        'Delete this wallet offer?'
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
                            colspan="7"
                            class="text-center py-5">

                            <i class="bi bi-wallet2 fs-2 text-muted d-block mb-2"></i>

                            <div class="text-muted">
                                No wallet offers found.
                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($walletOffers->hasPages())

        <div class="card-footer bg-white">

            {{ $walletOffers->links() }}

        </div>

        @endif

    </div>

</div>



{{-- ================================================================
    ADD WALLET OFFER
================================================================= --}}

<div
    class="modal fade"
    id="addWalletOfferModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form
                action="{{ route('admin.wallet-offers.store') }}"
                method="POST">

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title fw-bold">
                        Add Wallet Offer
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <div class="row g-3">


                        {{-- Title --}}
                        <div class="col-12">

                            <label class="form-label fw-semibold">
                                Offer Title
                            </label>

                            <input
                                type="text"
                                name="title"
                                class="form-control"
                                placeholder="Example: Recharge ₹500 and get 10% extra"
                                required>

                        </div>


                        {{-- Amount --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Recharge Amount
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    ₹
                                </span>

                                <input
                                    type="number"
                                    id="addWalletAmount"
                                    name="amount"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    required>

                            </div>

                        </div>


                        {{-- Percentage --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Add-on Percentage
                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    id="addWalletPercentage"
                                    name="add_on_percentage"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    required>

                                <span class="input-group-text">
                                    %
                                </span>

                            </div>

                        </div>


                        {{-- Calculation Preview --}}
                        <div class="col-12">

                            <div class="bg-light border rounded-3 p-3">

                                <div class="row g-3">

                                    <div class="col-md-4">

                                        <div class="small text-muted">
                                            Recharge
                                        </div>

                                        <div class="fw-semibold">

                                            ₹<span id="addRechargePreview">
                                                0.00
                                            </span>

                                        </div>

                                    </div>


                                    <div class="col-md-4">

                                        <div class="small text-muted">
                                            Bonus
                                        </div>

                                        <div class="fw-semibold text-success">

                                            +₹<span id="addBonusPreview">
                                                0.00
                                            </span>

                                        </div>

                                    </div>


                                    <div class="col-md-4">

                                        <div class="small text-muted">
                                            Wallet Credit
                                        </div>

                                        <div class="fw-bold text-primary">

                                            ₹<span id="addFinalPreview">
                                                0.00
                                            </span>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Description --}}
                        <div class="col-12">

                            <label class="form-label fw-semibold">
                                Description
                            </label>

                            <textarea
                                name="description"
                                class="form-control"
                                rows="4"
                                placeholder="Describe the wallet offer..."
                                required></textarea>

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

                        <i class="bi bi-check-lg me-1"></i>

                        Save Offer

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>



{{-- ================================================================
    EDIT WALLET OFFER
================================================================= --}}

<div
    class="modal fade"
    id="editWalletOfferModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form
                id="editWalletOfferForm"
                method="POST">

                @csrf
                @method('PUT')


                <div class="modal-header">

                    <h5 class="modal-title fw-bold">
                        Edit Wallet Offer
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

                            <label class="form-label fw-semibold">
                                Offer Title
                            </label>

                            <input
                                type="text"
                                id="editWalletTitle"
                                name="title"
                                class="form-control"
                                required>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Recharge Amount
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    ₹
                                </span>

                                <input
                                    type="number"
                                    id="editWalletAmount"
                                    name="amount"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    required>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Add-on Percentage
                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    id="editWalletPercentage"
                                    name="add_on_percentage"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    required>

                                <span class="input-group-text">
                                    %
                                </span>

                            </div>

                        </div>


                        {{-- Preview --}}
                        <div class="col-12">

                            <div class="bg-light border rounded-3 p-3">

                                <div class="row g-3">

                                    <div class="col-md-4">

                                        <div class="small text-muted">
                                            Recharge
                                        </div>

                                        <div class="fw-semibold">

                                            ₹<span id="editRechargePreview">
                                                0.00
                                            </span>

                                        </div>

                                    </div>


                                    <div class="col-md-4">

                                        <div class="small text-muted">
                                            Bonus
                                        </div>

                                        <div class="fw-semibold text-success">

                                            +₹<span id="editBonusPreview">
                                                0.00
                                            </span>

                                        </div>

                                    </div>


                                    <div class="col-md-4">

                                        <div class="small text-muted">
                                            Wallet Credit
                                        </div>

                                        <div class="fw-bold text-primary">

                                            ₹<span id="editFinalPreview">
                                                0.00
                                            </span>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="col-12">

                            <label class="form-label fw-semibold">
                                Description
                            </label>

                            <textarea
                                id="editWalletDescription"
                                name="description"
                                class="form-control"
                                rows="4"
                                required></textarea>

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

        /*
        |--------------------------------------------------------------------------
        | Wallet calculation
        |--------------------------------------------------------------------------
        */

        function calculateWalletOffer(
            amountInput,
            percentageInput,
            rechargePreview,
            bonusPreview,
            finalPreview
        ) {

            const amount =
                parseFloat(amountInput.value) || 0;

            const percentage =
                parseFloat(percentageInput.value) || 0;

            const bonus =
                (amount * percentage) / 100;

            const finalAmount =
                amount + bonus;


            rechargePreview.textContent =
                amount.toFixed(2);

            bonusPreview.textContent =
                bonus.toFixed(2);

            finalPreview.textContent =
                finalAmount.toFixed(2);
        }


        /*
        |--------------------------------------------------------------------------
        | Add Modal
        |--------------------------------------------------------------------------
        */

        const addAmount =
            document.getElementById('addWalletAmount');

        const addPercentage =
            document.getElementById('addWalletPercentage');


        function calculateAddOffer() {

            calculateWalletOffer(

                addAmount,

                addPercentage,

                document.getElementById(
                    'addRechargePreview'
                ),

                document.getElementById(
                    'addBonusPreview'
                ),

                document.getElementById(
                    'addFinalPreview'
                )

            );
        }


        addAmount.addEventListener(
            'input',
            calculateAddOffer
        );

        addPercentage.addEventListener(
            'input',
            calculateAddOffer
        );


        /*
        |--------------------------------------------------------------------------
        | Edit Modal
        |--------------------------------------------------------------------------
        */

        const editAmount =
            document.getElementById('editWalletAmount');

        const editPercentage =
            document.getElementById('editWalletPercentage');


        function calculateEditOffer() {

            calculateWalletOffer(

                editAmount,

                editPercentage,

                document.getElementById(
                    'editRechargePreview'
                ),

                document.getElementById(
                    'editBonusPreview'
                ),

                document.getElementById(
                    'editFinalPreview'
                )

            );
        }


        editAmount.addEventListener(
            'input',
            calculateEditOffer
        );

        editPercentage.addEventListener(
            'input',
            calculateEditOffer
        );


        /*
        |--------------------------------------------------------------------------
        | Populate Edit Modal
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll('.edit-wallet-offer-btn')
            .forEach(function(button) {

                button.addEventListener(
                    'click',
                    function() {

                        const id =
                            this.dataset.id;


                        document.getElementById(
                                'editWalletTitle'
                            ).value =
                            this.dataset.title;


                        editAmount.value =
                            this.dataset.amount;


                        editPercentage.value =
                            this.dataset.percentage;


                        document.getElementById(
                                'editWalletDescription'
                            ).value =
                            this.dataset.description;


                        document.getElementById(
                                'editWalletOfferForm'
                            ).action =
                            "{{ url('/admin/wallet-offers') }}/" +
                            id;


                        calculateEditOffer();

                    }
                );

            });

    });
</script>

@endsection