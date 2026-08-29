<div class="card">

    <div class="card-header">
        <h5 class="mb-0">Members</h5>
    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead>
                    <tr>
                        <th>Profile ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($members as $member)

                    <tr>

                        <td>
                            {{ $member->profile_id }}
                        </td>

                        <td>
                            {{ $member->full_name }}
                        </td>

                        <td>
                            {{ $member->email }}
                        </td>

                        <td>
                            {{ $member->mobile_number }}
                        </td>

                        <td>
                            {{ $member->gender }}
                        </td>

                        <td>
                            {{-- Use your existing age logic here --}}
                            {{ $member->age ?? '-' }}
                        </td>

                        <td>
                            {{ $member->active }}
                        </td>

                        <td>
                            <a
                                href="{{ route('admin.members.show', $member->id) }}"
                                class="btn btn-sm btn-primary">
                                View
                            </a>
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="8" class="text-center py-4">
                            No members found.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>