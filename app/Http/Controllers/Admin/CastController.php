<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CastController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Cast List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = $request->get('search');

        $casts = Cast::query()
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('cast', 'LIKE', '%'.$search.'%')
                        ->orWhere('religion', 'LIKE', '%'.$search.'%');
                });
            })
            ->orderBy('cast')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Religions
        |--------------------------------------------------------------------------
        */

        $religions = DB::connection('site')
            ->table('religions')
            ->select([
                'id',
                'religion',
            ])
            ->orderBy('religion')
            ->get();

        return view('admin.casts.index', [
            'casts' => $casts,
            'religions' => $religions,
            'search' => $search,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store Cast
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cast' => [
                'required',
                'string',
                'max:255',
            ],

            'religion' => [
                'required',
                'string',
                'max:123',
            ],
        ], [
            'cast.required' => 'Please enter a cast name.',

            'religion.required' => 'Please select a religion.',
        ]);

        Cast::create([
            'cast' => trim($validated['cast']),
            'religion' => trim($validated['religion']),
        ]);

        return redirect()
            ->route('admin.casts.index')
            ->with(
                'success',
                'Cast added successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Cast
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        $validated = $request->validate([
            'cast' => [
                'required',
                'string',
                'max:255',
            ],

            'religion' => [
                'required',
                'string',
                'max:123',
            ],
        ], [
            'cast.required' => 'Please enter a cast name.',

            'religion.required' => 'Please select a religion.',
        ]);

        $cast = Cast::find($id);

        if (! $cast) {
            abort(404, 'Cast not found.');
        }

        $cast->update([
            'cast' => trim($validated['cast']),
            'religion' => trim($validated['religion']),
        ]);

        return redirect()
            ->route('admin.casts.index')
            ->with(
                'success',
                'Cast updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Cast
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $cast = Cast::find($id);

        if (! $cast) {
            abort(404, 'Cast not found.');
        }

        $cast->delete();

        return redirect()
            ->route('admin.casts.index')
            ->with(
                'success',
                'Cast deleted successfully.'
            );
    }
}
