<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $countries = Country::query()
            ->when($search, function ($query) use ($search) {
                $query->where(
                    'name',
                    'LIKE',
                    '%'.$search.'%'
                );
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.countries.index', [
            'countries' => $countries,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
        ], [
            'name.required' => 'Please enter a country name.',
        ]);

        Country::create([
            'name' => trim($validated['name']),
        ]);

        return redirect()
            ->route('admin.countries.index')
            ->with(
                'success',
                'Country added successfully.'
            );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
        ], [
            'name.required' => 'Please enter a country name.',
        ]);

        $country = Country::find($id);

        if (! $country) {
            abort(404, 'Country not found.');
        }

        $country->update([
            'name' => trim($validated['name']),
        ]);

        return redirect()
            ->route('admin.countries.index')
            ->with(
                'success',
                'Country updated successfully.'
            );
    }

    public function destroy($id)
    {
        $country = Country::find($id);

        if (! $country) {
            abort(404, 'Country not found.');
        }

        $country->delete();

        return redirect()
            ->route('admin.countries.index')
            ->with(
                'success',
                'Country deleted successfully.'
            );
    }
}
