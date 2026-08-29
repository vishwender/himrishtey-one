<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaritalStatus;
use Illuminate\Http\Request;

class MaritalStatusController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $maritalStatuses = MaritalStatus::query()
            ->when($search, function ($query) use ($search) {
                $query->where(
                    'marital_status',
                    'LIKE',
                    '%'.$search.'%'
                );
            })
            ->orderBy('marital_status')
            ->paginate(10)
            ->withQueryString();

        return view('admin.marital-status.index', [
            'maritalStatuses' => $maritalStatuses,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'marital_status' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'marital_status.required' => 'Please enter a marital status.',
        ]);

        MaritalStatus::create([
            'marital_status' => trim($validated['marital_status']),
        ]);

        return redirect()
            ->route('admin.marital-status.index')
            ->with(
                'success',
                'Marital status added successfully.'
            );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'marital_status' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'marital_status.required' => 'Please enter a marital status.',
        ]);

        $maritalStatus = MaritalStatus::find($id);

        if (! $maritalStatus) {
            abort(404, 'Marital status not found.');
        }

        $maritalStatus->update([
            'marital_status' => trim($validated['marital_status']),
        ]);

        return redirect()
            ->route('admin.marital-status.index')
            ->with(
                'success',
                'Marital status updated successfully.'
            );
    }

    public function destroy($id)
    {
        $maritalStatus = MaritalStatus::find($id);

        if (! $maritalStatus) {
            abort(404, 'Marital status not found.');
        }

        $maritalStatus->delete();

        return redirect()
            ->route('admin.marital-status.index')
            ->with(
                'success',
                'Marital status deleted successfully.'
            );
    }
}
