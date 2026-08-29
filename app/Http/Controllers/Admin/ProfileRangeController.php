<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileRangeController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        $ranges = ProfileRange::query()
            ->orderByRaw('CAST(range_from AS UNSIGNED) ASC')
            ->paginate(20);

        return view(
            'admin.profile-ranges.index',
            compact('ranges')
        );
    }

    public function store(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        $validated = $request->validate([
            'range_from' => [
                'required',
                'numeric',
                'min:0',
            ],

            'range_to' => [
                'required',
                'numeric',
                'gte:range_from',
            ],

            'rate' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        ProfileRange::create([
            'range_from' => $validated['range_from'],
            'range_to' => $validated['range_to'],
            'rate' => $validated['rate'],
        ]);

        return redirect()
            ->route('admin.profile-ranges.index')
            ->with(
                'success',
                'Profile range added successfully.'
            );
    }

    public function update(
        Request $request,
        $id
    ) {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        $range = ProfileRange::findOrFail($id);

        $validated = $request->validate([
            'range_from' => [
                'required',
                'numeric',
                'min:0',
            ],

            'range_to' => [
                'required',
                'numeric',
                'gte:range_from',
            ],

            'rate' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $range->update([
            'range_from' => $validated['range_from'],
            'range_to' => $validated['range_to'],
            'rate' => $validated['rate'],
        ]);

        return redirect()
            ->route('admin.profile-ranges.index')
            ->with(
                'success',
                'Profile range updated successfully.'
            );
    }
}
