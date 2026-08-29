<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Http\Request;

class EmployerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $employers = Employer::query()
            ->when($search, function ($query) use ($search) {
                $query->where(
                    'employer',
                    'LIKE',
                    '%'.$search.'%'
                );
            })
            ->orderBy('employer')
            ->paginate(10)
            ->withQueryString();

        return view('admin.employers.index', [
            'employers' => $employers,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employer' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'employer.required' => 'Please enter an employer.',

            'employer.max' => 'Employer cannot exceed 255 characters.',
        ]);

        Employer::create([
            'employer' => trim($validated['employer']),
        ]);

        return redirect()
            ->route('admin.employers.index')
            ->with(
                'success',
                'Employer added successfully.'
            );
    }

    public function update(
        Request $request,
        $id
    ) {
        $validated = $request->validate([
            'employer' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'employer.required' => 'Please enter an employer.',

            'employer.max' => 'Employer cannot exceed 255 characters.',
        ]);

        $employer = Employer::find($id);

        if (! $employer) {
            abort(404, 'Employer not found.');
        }

        $employer->update([
            'employer' => trim($validated['employer']),
        ]);

        return redirect()
            ->route('admin.employers.index')
            ->with(
                'success',
                'Employer updated successfully.'
            );
    }

    public function destroy($id)
    {
        $employer = Employer::find($id);

        if (! $employer) {
            abort(404, 'Employer not found.');
        }

        $employer->delete();

        return redirect()
            ->route('admin.employers.index')
            ->with(
                'success',
                'Employer deleted successfully.'
            );
    }
}
