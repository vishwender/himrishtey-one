<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display website pages.
     */
    public function index()
    {
        $page = Page::first();

        return view('admin.pages.index', compact('page'));
    }

    /**
     * Update website pages.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'refund_policy' => [
                'required',
                'string',
            ],

            'privacy_policy' => [
                'required',
                'string',
            ],

            'terms_and_conditions' => [
                'required',
                'string',
            ],

            'about_us' => [
                'required',
                'string',
            ],
        ]);

        $page = Page::first();

        /*
        |--------------------------------------------------------------------------
        | Safety check
        |--------------------------------------------------------------------------
        */

        if (! $page) {
            return redirect()
                ->back()
                ->with('error', 'Pages configuration record was not found.');
        }

        $page->refund_policy = $validated['refund_policy'];

        $page->privacy_policy = $validated['privacy_policy'];

        $page->terms_and_conditions =
            $validated['terms_and_conditions'];

        $page->about_us = $validated['about_us'];

        $page->updated_at = now();

        $page->save();

        return redirect()
            ->route('admin.pages.index')
            ->with(
                'success',
                'Pages updated successfully.'
            );
    }
}
