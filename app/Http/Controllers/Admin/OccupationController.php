<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Occupation;
use Illuminate\Http\Request;

class OccupationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Occupation List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = $request->get('search');

        $occupations = Occupation::query()
            ->when($search, function ($query) use ($search) {

                $query->where(
                    'occupation',
                    'LIKE',
                    '%'.$search.'%'
                );
            })
            ->orderBy('occupation')
            ->paginate(10)
            ->withQueryString();

        return view('admin.occupations.index', [
            'occupations' => $occupations,
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
            'occupation' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'occupation.required' => 'Please enter an occupation.',

            'occupation.max' => 'Occupation cannot exceed 255 characters.',
        ]);

        Occupation::create([
            'occupation' => trim($validated['occupation']),
            'status' => 1,
        ]);

        return redirect()
            ->route('admin.occupations.index')
            ->with(
                'success',
                'Occupation added successfully.'
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
            'occupation' => [
                'required',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                'in:0,1',
            ],
        ]);

        $occupation = Occupation::find($id);

        if (! $occupation) {
            abort(404, 'Occupation not found.');
        }

        $occupation->update([
            'occupation' => trim(
                $validated['occupation']
            ),

            'status' => (int) $validated['status'],
        ]);

        return redirect()
            ->route('admin.occupations.index')
            ->with(
                'success',
                'Occupation updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus($id)
    {
        $occupation = Occupation::find($id);

        if (! $occupation) {
            abort(404, 'Occupation not found.');
        }

        $occupation->status =
            $occupation->status == 1 ? 0 : 1;

        $occupation->save();

        return redirect()
            ->route('admin.occupations.index')
            ->with(
                'success',
                'Occupation status updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $occupation = Occupation::find($id);

        if (! $occupation) {
            abort(404, 'Occupation not found.');
        }

        $occupation->delete();

        return redirect()
            ->route('admin.occupations.index')
            ->with(
                'success',
                'Occupation deleted successfully.'
            );
    }
}
