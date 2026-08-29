<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        $query = Offer::query();

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        $offers = $query
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view(
            'admin.offers.index',
            compact('offers')
        );
    }

    public function store(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'offer_date' => [
                'required',
                'date',
            ],

            'offer_time' => [
                'required',
            ],

            'status' => [
                'required',
                'in:Active,Inactive',
            ],

            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:3072',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Upload Image
        |--------------------------------------------------------------------------
        */

        $filename = null;

        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $filename =
                'offer-'.
                time().
                '.'.
                $file->getClientOriginalExtension();

            $destination =
                public_path('images/offers');

            if (! File::exists($destination)) {
                File::makeDirectory(
                    $destination,
                    0755,
                    true
                );
            }

            $file->move(
                $destination,
                $filename
            );
        }

        Offer::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $filename,
            'offer_date' => $validated['offer_date'],
            'offer_time' => $validated['offer_time'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.offers.index')
            ->with(
                'success',
                'Offer created successfully.'
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

        $offer = Offer::findOrFail($id);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'offer_date' => [
                'required',
                'date',
            ],

            'offer_time' => [
                'required',
            ],

            'status' => [
                'required',
                'in:Active,Inactive',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:3072',
            ],
        ]);

        $filename = $offer->image;

        /*
        |--------------------------------------------------------------------------
        | Replace Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            $oldImage =
                public_path(
                    'images/offers/'.
                        $offer->image
                );

            if (
                $offer->image &&
                File::exists($oldImage)
            ) {
                File::delete($oldImage);
            }

            $file = $request->file('image');

            $filename =
                'offer-'.
                time().
                '.'.
                $file->getClientOriginalExtension();

            $destination =
                public_path('images/offers');

            if (! File::exists($destination)) {

                File::makeDirectory(
                    $destination,
                    0755,
                    true
                );
            }

            $file->move(
                $destination,
                $filename
            );
        }

        $offer->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $filename,
            'offer_date' => $validated['offer_date'],
            'offer_time' => $validated['offer_time'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.offers.index')
            ->with(
                'success',
                'Offer updated successfully.'
            );
    }

    public function destroy($id)
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        $offer = Offer::findOrFail($id);

        if ($offer->image) {

            $imagePath =
                public_path(
                    'images/offers/'.
                        $offer->image
                );

            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $offer->delete();

        return redirect()
            ->route('admin.offers.index')
            ->with(
                'success',
                'Offer deleted successfully.'
            );
    }

    public function toggleStatus($id)
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        $offer = Offer::findOrFail($id);

        $offer->status =
            $offer->status === 'Active'
            ? 'Inactive'
            : 'Active';

        $offer->save();

        return redirect()
            ->route('admin.offers.index')
            ->with(
                'success',
                'Offer status updated successfully.'
            );
    }
}
