<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Height;
use Illuminate\Http\Request;

class HeightController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $heights = Height::query()
            ->when($search, function ($query) use ($search) {
                $query->where('height', 'LIKE', '%'.$search.'%')
                    ->orWhere(
                        'height_value',
                        'LIKE',
                        '%'.$search.'%'
                    );
            })
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.heights.index', [
            'heights' => $heights,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'height' => [
                'required',
                'string',
                'max:255',
            ],
            'height_value' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        Height::create([
            'height' => trim($validated['height']),
            'height_value' => trim($validated['height_value']),
        ]);

        return redirect()
            ->route('admin.heights.index')
            ->with(
                'success',
                'Height added successfully.'
            );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'height' => [
                'required',
                'string',
                'max:255',
            ],
            'height_value' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $height = Height::find($id);

        if (! $height) {
            abort(404, 'Height not found.');
        }

        $height->update([
            'height' => trim($validated['height']),
            'height_value' => trim($validated['height_value']),
        ]);

        return redirect()
            ->route('admin.heights.index')
            ->with(
                'success',
                'Height updated successfully.'
            );
    }

    public function destroy($id)
    {
        $height = Height::find($id);

        if (! $height) {
            abort(404, 'Height not found.');
        }

        $height->delete();

        return redirect()
            ->route('admin.heights.index')
            ->with(
                'success',
                'Height deleted successfully.'
            );
    }
}
