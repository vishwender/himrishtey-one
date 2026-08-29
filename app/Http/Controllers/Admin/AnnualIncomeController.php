<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnnualIncome;
use Illuminate\Http\Request;

class AnnualIncomeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = $request->get('search');

        $annualIncomes = AnnualIncome::query()
            ->when($search, function ($query) use ($search) {

                $query->where(
                    'annual_income',
                    'LIKE',
                    '%'.$search.'%'
                );
            })
            ->orderBy('display_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.annual-incomes.index', [
            'annualIncomes' => $annualIncomes,
            'search' => $search,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'annual_income' => [
                'required',
                'string',
                'max:255',
            ],

            'display_order' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [
            'annual_income.required' => 'Please enter an annual income.',

            'display_order.required' => 'Please enter the display order.',

            'display_order.integer' => 'Display order must be a number.',

            'display_order.min' => 'Display order must be at least 1.',
        ]);

        AnnualIncome::create([
            'annual_income' => trim($validated['annual_income']),

            'display_order' => (int) $validated['display_order'],
        ]);

        return redirect()
            ->route('admin.annual-incomes.index')
            ->with(
                'success',
                'Annual income added successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        $validated = $request->validate([
            'annual_income' => [
                'required',
                'string',
                'max:255',
            ],

            'display_order' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $annualIncome = AnnualIncome::find($id);

        if (! $annualIncome) {
            abort(404, 'Annual income not found.');
        }

        $annualIncome->update([
            'annual_income' => trim($validated['annual_income']),

            'display_order' => (int) $validated['display_order'],
        ]);

        return redirect()
            ->route('admin.annual-incomes.index')
            ->with(
                'success',
                'Annual income updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $annualIncome = AnnualIncome::find($id);

        if (! $annualIncome) {
            abort(404, 'Annual income not found.');
        }

        $annualIncome->delete();

        return redirect()
            ->route('admin.annual-incomes.index')
            ->with(
                'success',
                'Annual income deleted successfully.'
            );
    }
}
