@extends('admin.layout')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="mb-4">

        <h3 class="mb-1">
            Member Activity
        </h3>

        <p class="text-muted mb-0">
            Search for a member to view their activity.
        </p>

    </div>


    {{-- Search Card --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <h5 class="mb-3">
                Search Member
            </h5>

            <div class="row">

                <div class="col-lg-8">

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="text"
                            id="memberActivitySearch"
                            class="form-control"
                            placeholder="Search by Profile ID, Name, Email or Mobile Number"
                            autocomplete="off">

                        <button
                            type="button"
                            id="clearMemberSearch"
                            class="btn btn-outline-secondary">

                            Clear

                        </button>

                    </div>

                </div>

            </div>


            {{-- Search Results --}}
            <div
                id="memberSearchResults"
                class="mt-4"
                style="display:none;">

            </div>


            {{-- Loading --}}
            <div
                id="memberSearchLoading"
                class="text-muted mt-3"
                style="display:none;">

                <div class="d-flex align-items-center">

                    <div
                        class="spinner-border spinner-border-sm me-2"
                        role="status">
                    </div>

                    Searching members...

                </div>

            </div>


            {{-- No Results --}}
            <div
                id="memberSearchEmpty"
                class="text-muted text-center py-4"
                style="display:none;">

                <i class="bi bi-search fs-3 d-block mb-2"></i>

                No members found.

            </div>

        </div>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const searchInput = document.getElementById('memberActivitySearch');
        const resultsBox = document.getElementById('memberSearchResults');
        const loadingBox = document.getElementById('memberSearchLoading');
        const emptyBox = document.getElementById('memberSearchEmpty');
        const clearButton = document.getElementById('clearMemberSearch');

        let searchTimer = null;


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        searchInput.addEventListener('input', function() {

            const search = this.value.trim();

            clearTimeout(searchTimer);

            resultsBox.style.display = 'none';
            resultsBox.innerHTML = '';

            emptyBox.style.display = 'none';


            if (search.length < 2) {

                loadingBox.style.display = 'none';

                return;
            }


            searchTimer = setTimeout(function() {

                searchMembers(search);

            }, 300);

        });


        /*
        |--------------------------------------------------------------------------
        | AJAX Search
        |--------------------------------------------------------------------------
        */

        function searchMembers(search) {

            loadingBox.style.display = 'block';
            emptyBox.style.display = 'none';
            resultsBox.style.display = 'none';


            fetch(
                    "{{ route('admin.activities.search-members') }}?search=" +
                    encodeURIComponent(search), {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                )
                .then(response => {

                    if (!response.ok) {
                        throw new Error('Search request failed.');
                    }

                    return response.json();

                })
                .then(members => {

                    loadingBox.style.display = 'none';


                    if (!members.length) {

                        emptyBox.style.display = 'block';

                        return;
                    }


                    let html = `

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">

                            <tr>

                                <th>Profile ID</th>

                                <th>Name</th>

                                <th>Email</th>

                                <th>Mobile</th>

                                <th width="100">Action</th>

                            </tr>

                        </thead>

                        <tbody>
            `;


                    members.forEach(member => {

                        html += `

                    <tr>

                        <td>

                            <strong>
                                ${escapeHtml(member.profile_id ?? '')}
                            </strong>

                        </td>

                        <td>
                            ${escapeHtml(member.full_name ?? '')}
                        </td>

                        <td>
                            ${escapeHtml(member.email ?? '')}
                        </td>

                        <td>
                            ${escapeHtml(member.mobile_number ?? '')}
                        </td>

                        <td>

                            <a
                                href="${memberActivityUrl(member.id)}"
                                class="btn btn-sm btn-primary">

                                View

                            </a>

                        </td>

                    </tr>

                `;

                    });


                    html += `

                        </tbody>

                    </table>

                </div>

            `;


                    resultsBox.innerHTML = html;
                    resultsBox.style.display = 'block';

                })
                .catch(error => {

                    console.error(error);

                    loadingBox.style.display = 'none';

                    resultsBox.innerHTML = `

                <div class="alert alert-danger">

                    Unable to search members.
                    Please try again.

                </div>

            `;

                    resultsBox.style.display = 'block';

                });

        }


        /*
        |--------------------------------------------------------------------------
        | Member Activity URL
        |--------------------------------------------------------------------------
        */

        function memberActivityUrl(memberId) {

            return "{{ url('/admin/activities/member') }}/" + memberId;

        }


        /*
        |--------------------------------------------------------------------------
        | Clear Search
        |--------------------------------------------------------------------------
        */

        clearButton.addEventListener('click', function() {

            searchInput.value = '';

            resultsBox.innerHTML = '';
            resultsBox.style.display = 'none';

            loadingBox.style.display = 'none';
            emptyBox.style.display = 'none';

            searchInput.focus();

        });


        /*
        |--------------------------------------------------------------------------
        | Prevent HTML Injection
        |--------------------------------------------------------------------------
        */

        function escapeHtml(value) {

            const div = document.createElement('div');

            div.textContent = value;

            return div.innerHTML;

        }

    });
</script>

@endsection