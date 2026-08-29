@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h5 class="mb-1">
                        Annual Income
                    </h5>

                    <div
                        class="border-bottom"
                        style="width:60px;">
                    </div>

                </div>


                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addAnnualIncomeModal">

                    <i class="bi bi-plus-lg me-1"></i>
                    Add Income

                </button>

            </div>


            {{-- Success --}}
            @if(session('success'))

            <div
                class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

            @endif


            {{-- Validation --}}
            @if($errors->any())

            <div
                class="alert alert-danger alert-dismissible fade show">

                <ul class="mb-0">

                    @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                    @endforeach

                </ul>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

            @endif


            {{-- Search --}}
            <div class="row mb-3">

                <div class="col-md-4 ms-auto">

                    <form
                        method="GET"
                        action="{{ route('admin.annual-incomes.index') }}">

                        <div class="input-group">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search annual income..."
                                value="{{ $search }}">

                            <button
                                class="btn btn-outline-secondary"
                                type="submit">

                                <i class="bi bi-search"></i>

                            </button>


                            @if($search)

                            <a
                                href="{{ route('admin.annual-incomes.index') }}"
                                class="btn btn-outline-secondary">

                                <i class="bi bi-x-lg"></i>

                            </a>

                            @endif

                        </div>

                    </form>

                </div>

            </div>


            {{-- Table --}}
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th style="width:100px;">
                                ID
                            </th>

                            <th>
                                Annual Income
                            </th>

                            <th style="width:180px;">
                                Display Order
                            </th>

                            <th style="width:160px;">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($annualIncomes as $income)

                        <tr>

                            <td>
                                {{ $income->id }}
                            </td>

                            <td>
                                <span class="fw-medium">
                                    {{ $income->annual_income }}
                                </span>
                            </td>

                            <td>

                                <span class="badge bg-light text-dark border">

                                    {{ $income->display_order }}

                                </span>

                            </td>

                            <td>

                                <div class="d-flex gap-2">

                                    {{-- Edit --}}
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editAnnualIncomeModal{{ $income->id }}"
                                        title="Edit">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>


                                    {{-- Delete --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.annual-incomes.destroy',
                                                $income->id
                                            ) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this income option?');">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>


                        {{-- Edit Modal --}}
                        <div
                            class="modal fade"
                            id="editAnnualIncomeModal{{ $income->id }}"
                            tabindex="-1"
                            aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered">

                                <div class="modal-content">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'admin.annual-incomes.update',
                                                $income->id
                                            ) }}">

                                        @csrf
                                        @method('PUT')


                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Edit Annual Income
                                            </h5>

                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                            </button>

                                        </div>


                                        <div class="modal-body">

                                            <div class="mb-3">

                                                <label
                                                    class="form-label">

                                                    Annual Income

                                                </label>

                                                <input
                                                    type="text"
                                                    name="annual_income"
                                                    class="form-control"
                                                    value="{{ $income->annual_income }}"
                                                    required
                                                    maxlength="255">

                                            </div>


                                            <div class="mb-3">

                                                <label
                                                    class="form-label">

                                                    Display Order

                                                </label>

                                                <input
                                                    type="number"
                                                    name="display_order"
                                                    class="form-control"
                                                    value="{{ $income->display_order }}"
                                                    min="1"
                                                    required>

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

                                                <i class="bi bi-check-circle me-1"></i>
                                                Update

                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                        @empty

                        <tr>

                            <td
                                colspan="4"
                                class="text-center py-5">

                                <i
                                    class="bi bi-cash-stack fs-1 text-muted">
                                </i>

                                <p class="text-muted mb-0 mt-2">
                                    No annual income records found.
                                </p>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">

                <div class="text-muted small">

                    Showing
                    {{ $annualIncomes->firstItem() ?? 0 }}
                    to
                    {{ $annualIncomes->lastItem() ?? 0 }}
                    of
                    {{ $annualIncomes->total() }}
                    entries

                </div>

                <div>
                    {{ $annualIncomes->links() }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    ADD MODAL
========================================================= --}}

<div
    class="modal fade"
    id="addAnnualIncomeModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('admin.annual-incomes.store') }}">

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title">
                        Add Annual Income
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <div class="mb-3">

                        <label
                            class="form-label">

                            Annual Income

                        </label>

                        <input
                            type="text"
                            name="annual_income"
                            class="form-control"
                            placeholder="e.g. 25 Lakh"
                            required
                            maxlength="255">

                    </div>


                    <div class="mb-3">

                        <label
                            class="form-label">

                            Display Order

                        </label>

                        <input
                            type="number"
                            name="display_order"
                            class="form-control"
                            min="1"
                            placeholder="e.g. 25"
                            required>

                        <div class="form-text">

                            Lower numbers appear first.

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

                        <i class="bi bi-plus-circle me-1"></i>
                        Add Income

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection