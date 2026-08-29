<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRating;
use Illuminate\Http\Request;

class UserRatingController extends Controller
{
    public function index(Request $request)
    {
        $query = UserRating::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('profile_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $ratings = $query
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.user-ratings.index', compact('ratings'));
    }

    public function show($id)
    {
        $rating = UserRating::findOrFail($id);

        return view('admin.user-ratings.show', compact('rating'));
    }

    public function destroy($id)
    {
        $rating = UserRating::findOrFail($id);

        $rating->delete();

        return redirect()
            ->route('admin.user-ratings.index')
            ->with('success', 'Rating deleted successfully.');
    }
}
