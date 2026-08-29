<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuccessStoryController extends Controller
{
    public function index()
    {
        $stories = SuccessStory::orderByDesc('id')
            ->paginate(15);

        return view('admin.success-stories.index', compact('stories'));
    }

    public function create()
    {
        return view('admin.success-stories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'detail' => 'required|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status' => 'nullable|boolean',
        ]);

        $story = new SuccessStory;

        $story->groom_name = $validated['groom_name'];
        $story->bride_name = $validated['bride_name'];
        $story->detail = $validated['detail'];
        $story->status = $request->boolean('status');

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            $filename = 'ss-photo-'.time().'.'.
                $file->getClientOriginalExtension();

            $file->storeAs(
                'success-stories',
                $filename,
                'public'
            );

            $story->photo = $filename;
        }

        $story->save();

        return redirect()
            ->route('admin.success-stories.index')
            ->with('success', 'Success story added successfully.');
    }

    public function edit($id)
    {
        $story = SuccessStory::findOrFail($id);

        return view(
            'admin.success-stories.edit',
            compact('story')
        );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'detail' => 'required|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status' => 'nullable|boolean',
        ]);

        $story = SuccessStory::findOrFail($id);

        $story->groom_name = $validated['groom_name'];
        $story->bride_name = $validated['bride_name'];
        $story->detail = $validated['detail'];
        $story->status = $request->boolean('status');

        if ($request->hasFile('photo')) {

            // Delete old photo
            if ($story->photo) {
                Storage::disk('public')->delete(
                    'success-stories/'.$story->photo
                );
            }

            $file = $request->file('photo');

            $filename = 'ss-photo-'.time().'.'.
                $file->getClientOriginalExtension();

            $file->storeAs(
                'success-stories',
                $filename,
                'public'
            );

            $story->photo = $filename;
        }

        $story->save();

        return redirect()
            ->route('admin.success-stories.index')
            ->with('success', 'Success story updated successfully.');
    }

    public function destroy($id)
    {
        $story = SuccessStory::findOrFail($id);

        if ($story->photo) {
            Storage::disk('public')->delete(
                'success-stories/'.$story->photo
            );
        }

        $story->delete();

        return redirect()
            ->route('admin.success-stories.index')
            ->with('success', 'Success story deleted successfully.');
    }

    public function status($id)
    {
        $story = SuccessStory::findOrFail($id);

        $story->status = ! $story->status;
        $story->save();

        return back()
            ->with('success', 'Status updated successfully.');
    }
}
