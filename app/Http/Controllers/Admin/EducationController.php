<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Education List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = $request->get('search');

        $educations = Education::query()
            ->when($search, function ($query) use ($search) {

                $query->where(
                    'education',
                    'LIKE',
                    '%'.$search.'%'
                );
            })
            ->orderBy('education')
            ->paginate(10)
            ->withQueryString();

        return view('admin.educations.index', [
            'educations' => $educations,
            'search' => $search,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store Education
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'education' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'education.required' => 'Please enter an education name.',

            'education.max' => 'Education name cannot exceed 255 characters.',
        ]);

        Education::create([
            'education' => trim(
                $validated['education']
            ),
        ]);

        return redirect()
            ->route('admin.educations.index')
            ->with(
                'success',
                'Education added successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Education
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        $validated = $request->validate([
            'education' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'education.required' => 'Please enter an education name.',

            'education.max' => 'Education name cannot exceed 255 characters.',
        ]);

        $education = Education::find($id);

        if (! $education) {

            abort(
                404,
                'Education not found.'
            );
        }

        $education->update([
            'education' => trim(
                $validated['education']
            ),
        ]);

        return redirect()
            ->route('admin.educations.index')
            ->with(
                'success',
                'Education updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Education
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $education = Education::find($id);

        if (! $education) {

            abort(
                404,
                'Education not found.'
            );
        }

        $education->delete();

        return redirect()
            ->route('admin.educations.index')
            ->with(
                'success',
                'Education deleted successfully.'
            );
    }
}
