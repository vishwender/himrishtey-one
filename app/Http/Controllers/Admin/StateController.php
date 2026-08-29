<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $states = State::query()
            ->with('country')
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where(
                        'states.name',
                        'LIKE',
                        '%'.$search.'%'
                    )
                        ->orWhereHas('country', function ($countryQuery) use ($search) {

                            $countryQuery->where(
                                'name',
                                'LIKE',
                                '%'.$search.'%'
                            );
                        });
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $countries = Country::query()
            ->orderBy('name')
            ->get();

        return view('admin.states.index', [
            'states' => $states,
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
                'max:255',
            ],

            'country_id' => [
                'required',
                'integer',
                'exists:site.countries,id',
            ],
        ], [
            'name.required' => 'Please enter a state name.',

            'country_id.required' => 'Please select a country.',

            'country_id.exists' => 'The selected country is invalid.',
        ]);

        State::create([
            'name' => trim($validated['name']),
            'country_id' => $validated['country_id'],
        ]);

        return redirect()
            ->route('admin.states.index')
            ->with(
                'success',
                'State added successfully.'
            );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'country_id' => [
                'required',
                'integer',
                'exists:site.countries,id',
            ],
        ], [
            'name.required' => 'Please enter a state name.',

            'country_id.required' => 'Please select a country.',

            'country_id.exists' => 'The selected country is invalid.',
        ]);

        $state = State::find($id);

        if (! $state) {
            abort(404, 'State not found.');
        }

        $state->update([
            'name' => trim($validated['name']),
            'country_id' => $validated['country_id'],
        ]);

        return redirect()
            ->route('admin.states.index')
            ->with(
                'success',
                'State updated successfully.'
            );
    }

    public function destroy($id)
    {
        $state = State::find($id);

        if (! $state) {
            abort(404, 'State not found.');
        }

        $state->delete();

        return redirect()
            ->route('admin.states.index')
            ->with(
                'success',
                'State deleted successfully.'
            );
    }
}
