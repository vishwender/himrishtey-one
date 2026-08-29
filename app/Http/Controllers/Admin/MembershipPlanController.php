<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\MembershipType;
use Illuminate\Http\Request;

class MembershipPlanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $plans = MembershipPlan::query()
            ->with('membershipType')
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where(
                        'plan_name',
                        'LIKE',
                        '%'.$search.'%'
                    )

                        ->orWhere(
                            'membership_type',
                            'LIKE',
                            '%'.$search.'%'
                        )

                        ->orWhere(
                            'duration_days',
                            'LIKE',
                            '%'.$search.'%'
                        );
                });
            })
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $membershipTypes = MembershipType::query()
            ->orderBy('id')
            ->get();

        return view(
            'admin.membership-plans.index',
            compact(
                'plans',
                'membershipTypes',
                'search'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'membership_type' => [
                'required',
                'integer',
                'exists:site.membership_type,id',
            ],

            'plan_name' => [
                'required',
                'string',
                'max:255',
            ],

            'duration_days' => [
                'required',
                'integer',
                'min:1',
            ],

            'view_contact' => [
                'required',
                'integer',
                'min:0',
            ],

            'view_profile' => [
                'required',
                'integer',
                'min:0',
            ],

            'plan_cost' => [
                'required',
                'integer',
                'min:0',
            ],

            'discount_percentage' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],
        ]);

        $finalCost = $this->calculateFinalCost(
            $validated['plan_cost'],
            $validated['discount_percentage']
        );

        MembershipPlan::create([
            'membership_type' => $validated['membership_type'],
            'plan_name' => trim($validated['plan_name']),
            'duration_days' => $validated['duration_days'],
            'view_contact' => $validated['view_contact'],
            'view_profile' => $validated['view_profile'],
            'plan_cost' => $validated['plan_cost'],
            'discount_percentage' => $validated['discount_percentage'],
            'final_cost' => $finalCost,
        ]);

        return redirect()
            ->route('admin.membership-plans.index')
            ->with(
                'success',
                'Membership plan added successfully.'
            );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'membership_type' => [
                'required',
                'integer',
                'exists:site.membership_type,id',
            ],

            'plan_name' => [
                'required',
                'string',
                'max:255',
            ],

            'duration_days' => [
                'required',
                'integer',
                'min:1',
            ],

            'view_contact' => [
                'required',
                'integer',
                'min:0',
            ],

            'view_profile' => [
                'required',
                'integer',
                'min:0',
            ],

            'plan_cost' => [
                'required',
                'integer',
                'min:0',
            ],

            'discount_percentage' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],
        ]);

        $plan = MembershipPlan::findOrFail($id);

        $finalCost = $this->calculateFinalCost(
            $validated['plan_cost'],
            $validated['discount_percentage']
        );

        $plan->update([
            'membership_type' => $validated['membership_type'],
            'plan_name' => trim($validated['plan_name']),
            'duration_days' => $validated['duration_days'],
            'view_contact' => $validated['view_contact'],
            'view_profile' => $validated['view_profile'],
            'plan_cost' => $validated['plan_cost'],
            'discount_percentage' => $validated['discount_percentage'],
            'final_cost' => $finalCost,
        ]);

        return redirect()
            ->route('admin.membership-plans.index')
            ->with(
                'success',
                'Membership plan updated successfully.'
            );
    }

    public function destroy($id)
    {
        $plan = MembershipPlan::findOrFail($id);

        $plan->delete();

        return redirect()
            ->route('admin.membership-plans.index')
            ->with(
                'success',
                'Membership plan deleted successfully.'
            );
    }

    private function calculateFinalCost(
        int $planCost,
        int $discountPercentage
    ): int {
        if ($planCost <= 0) {
            return 0;
        }

        return (int) round(
            $planCost -
                ($planCost * $discountPercentage / 100)
        );
    }
}
