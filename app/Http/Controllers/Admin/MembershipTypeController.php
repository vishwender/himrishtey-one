<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipType;
use Illuminate\Http\Request;

class MembershipTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $membershipTypes = MembershipType::query()
            ->withCount('plans')
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where(
                        'plan_name',
                        'LIKE',
                        '%'.$search.'%'
                    )

                        ->orWhere(
                            'plan_description',
                            'LIKE',
                            '%'.$search.'%'
                        );
                });
            })
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.membership-types.index',
            compact(
                'membershipTypes',
                'search'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([

            'plan_name' => [
                'required',
                'string',
                'max:255',
            ],

            'plan_guide' => [
                'required',
                'string',
            ],

            'plan_description' => [
                'required',
                'string',
            ],

            'terms_and_conditions' => [
                'required',
                'string',
            ],

        ]);

        MembershipType::create($validated);

        return redirect()
            ->route('admin.membership-types.index')
            ->with(
                'success',
                'Membership type added successfully.'
            );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([

            'plan_name' => [
                'required',
                'string',
                'max:255',
            ],

            'plan_guide' => [
                'required',
                'string',
            ],

            'plan_description' => [
                'required',
                'string',
            ],

            'terms_and_conditions' => [
                'required',
                'string',
            ],

        ]);

        $membershipType = MembershipType::findOrFail($id);

        $membershipType->update($validated);

        return redirect()
            ->route('admin.membership-types.index')
            ->with(
                'success',
                'Membership type updated successfully.'
            );
    }

    public function destroy($id)
    {
        $membershipType = MembershipType::findOrFail($id);

        if ($membershipType->plans()->exists()) {

            return redirect()
                ->route('admin.membership-types.index')
                ->with(
                    'error',
                    'This membership type cannot be deleted because membership plans are associated with it.'
                );
        }

        $membershipType->delete();

        return redirect()
            ->route('admin.membership-types.index')
            ->with(
                'success',
                'Membership type deleted successfully.'
            );
    }
}
