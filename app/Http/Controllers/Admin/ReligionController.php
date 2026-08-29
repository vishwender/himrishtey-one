<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Religion;
use Illuminate\Http\Request;

class ReligionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Religion List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = $request->get('search');

        $religions = Religion::query()
            ->when($search, function ($query) use ($search) {

                $query->where(
                    'religion',
                    'LIKE',
                    '%'.$search.'%'
                );
            })
            ->orderBy('religion')
            ->paginate(10)
            ->withQueryString();

        return view('admin.religions.index', [
            'religions' => $religions,
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
            'religion' => [
                'required',
                'string',
                'max:100',
            ],
        ], [
            'religion.required' => 'Please enter a religion.',

            'religion.max' => 'Religion cannot exceed 100 characters.',
        ]);

        Religion::create([
            'religion' => trim($validated['religion']),
        ]);

        return redirect()
            ->route('admin.religions.index')
            ->with(
                'success',
                'Religion added successfully.'
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
            'religion' => [
                'required',
                'string',
                'max:100',
            ],
        ], [
            'religion.required' => 'Please enter a religion.',

            'religion.max' => 'Religion cannot exceed 100 characters.',
        ]);

        $religion = Religion::find($id);

        if (! $religion) {
            abort(404, 'Religion not found.');
        }

        $religion->update([
            'religion' => trim($validated['religion']),
        ]);

        return redirect()
            ->route('admin.religions.index')
            ->with(
                'success',
                'Religion updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $religion = Religion::find($id);

        if (! $religion) {
            abort(404, 'Religion not found.');
        }

        $religion->delete();

        return redirect()
            ->route('admin.religions.index')
            ->with(
                'success',
                'Religion deleted successfully.'
            );
    }
}
