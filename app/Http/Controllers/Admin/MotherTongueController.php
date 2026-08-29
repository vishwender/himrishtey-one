<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MotherTongue;
use Illuminate\Http\Request;

class MotherTongueController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $motherTongues = MotherTongue::query()
            ->when($search, function ($query) use ($search) {
                $query->where(
                    'mother_tongue',
                    'LIKE',
                    '%'.$search.'%'
                );
            })
            ->orderBy('mother_tongue')
            ->paginate(10)
            ->withQueryString();

        return view('admin.mother-tongues.index', [
            'motherTongues' => $motherTongues,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mother_tongue' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'mother_tongue.required' => 'Please enter a mother tongue.',
        ]);

        MotherTongue::create([
            'mother_tongue' => trim($validated['mother_tongue']),
        ]);

        return redirect()
            ->route('admin.mother-tongues.index')
            ->with(
                'success',
                'Mother tongue added successfully.'
            );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'mother_tongue' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'mother_tongue.required' => 'Please enter a mother tongue.',
        ]);

        $motherTongue = MotherTongue::find($id);

        if (! $motherTongue) {
            abort(404, 'Mother tongue not found.');
        }

        $motherTongue->update([
            'mother_tongue' => trim($validated['mother_tongue']),
        ]);

        return redirect()
            ->route('admin.mother-tongues.index')
            ->with(
                'success',
                'Mother tongue updated successfully.'
            );
    }

    public function destroy($id)
    {
        $motherTongue = MotherTongue::find($id);

        if (! $motherTongue) {
            abort(404, 'Mother tongue not found.');
        }

        $motherTongue->delete();

        return redirect()
            ->route('admin.mother-tongues.index')
            ->with(
                'success',
                'Mother tongue deleted successfully.'
            );
    }
}
