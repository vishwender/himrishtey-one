<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $cities = City::query()
            ->with('state.country')
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    // City
                    $q->where(
                        'cities.name',
                        'LIKE',
                        '%'.$search.'%'
                    )

                        // State
                        ->orWhereHas('state', function ($stateQuery) use ($search) {

                            $stateQuery->where(
                                'name',
                                'LIKE',
                                '%'.$search.'%'
                            );
                        })

                        // Country
                        ->orWhereHas('state.country', function ($countryQuery) use ($search) {

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

        /*
        |--------------------------------------------------------------------------
        | States
        |--------------------------------------------------------------------------
        |
        | Used for the State dropdown.
        |
        */

        $states = State::query()
            ->with('country')
            ->orderBy('name')
            ->get();

        return view('admin.cities.index', [
            'cities' => $cities,
            'states' => $states,
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

            'state_id' => [
                'required',
                'integer',
                'exists:site.states,id',
            ],
        ], [
            'name.required' => 'Please enter a city name.',

            'state_id.required' => 'Please select a state.',

            'state_id.exists' => 'The selected state is invalid.',
        ]);

        City::create([
            'name' => trim($validated['name']),
            'state_id' => $validated['state_id'],
        ]);

        return redirect()
            ->route('admin.cities.index')
            ->with(
                'success',
                'City added successfully.'
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

            'state_id' => [
                'required',
                'integer',
                'exists:site.states,id',
            ],
        ], [
            'name.required' => 'Please enter a city name.',

            'state_id.required' => 'Please select a state.',

            'state_id.exists' => 'The selected state is invalid.',
        ]);

        $city = City::find($id);

        if (! $city) {
            abort(404, 'City not found.');
        }

        $city->update([
            'name' => trim($validated['name']),
            'state_id' => $validated['state_id'],
        ]);

        return redirect()
            ->route('admin.cities.index')
            ->with(
                'success',
                'City updated successfully.'
            );
    }

    public function destroy($id)
    {
        $city = City::find($id);

        if (! $city) {
            abort(404, 'City not found.');
        }

        $city->delete();

        return redirect()
            ->route('admin.cities.index')
            ->with(
                'success',
                'City deleted successfully.'
            );
    }
}
