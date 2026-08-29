<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FamilyStatus;
use Illuminate\Http\Request;

class FamilyStatusController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $familyStatuses = FamilyStatus::query()
            ->when($search, function ($query) use ($search) {
                $query->where(
                    'value',
                    'LIKE',
                    '%'.$search.'%'
                );
            })
            ->orderBy('value')
            ->paginate(10)
            ->withQueryString();

        return view('admin.family-status.index', [
            'familyStatuses' => $familyStatuses,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'value' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'value.required' => 'Please enter a family status.',
        ]);

        FamilyStatus::create([
            'value' => trim($validated['value']),
        ]);

        return redirect()
            ->route('admin.family-status.index')
            ->with(
                'success',
                'Family status added successfully.'
            );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'value' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'value.required' => 'Please enter a family status.',
        ]);

        $familyStatus = FamilyStatus::find($id);

        if (! $familyStatus) {
            abort(404, 'Family status not found.');
        }

        $familyStatus->update([
            'value' => trim($validated['value']),
        ]);

        return redirect()
            ->route('admin.family-status.index')
            ->with(
                'success',
                'Family status updated successfully.'
            );
    }

    public function destroy($id)
    {
        $familyStatus = FamilyStatus::find($id);

        if (! $familyStatus) {
            abort(404, 'Family status not found.');
        }

        $familyStatus->delete();

        return redirect()
            ->route('admin.family-status.index')
            ->with(
                'success',
                'Family status deleted successfully.'
            );
    }
}
