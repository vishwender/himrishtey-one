<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\SiteDatabaseService;
use App\Services\SiteManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    public function switch(
        Request $request,
        Site $site,
        SiteManager $siteManager,
        SiteDatabaseService $databaseService
    ) {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        /*
        |--------------------------------------------------------------------------
        | Check admin access
        |--------------------------------------------------------------------------
        */

        if (! $admin->sites()
            ->where('sites.id', $site->id)
            ->exists()) {

            abort(403, 'You do not have access to this site.');
        }

        /*
        |--------------------------------------------------------------------------
        | Check site status
        |--------------------------------------------------------------------------
        */

        if (! $site->status) {

            return back()->withErrors([
                'site' => 'This site is inactive.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Store selected site
        |--------------------------------------------------------------------------
        */

        $siteManager->set($site);

        /*
        |--------------------------------------------------------------------------
        | Connect to selected database
        |--------------------------------------------------------------------------
        */

        $databaseService->connect($site);

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        $destination = $admin->hasRole('content-manager') && ! $admin->hasRole('super-admin')
            ? 'admin.blog-posts.index'
            : 'admin.dashboard';

        if ($admin->isMemberManager()) {
            $destination = 'admin.dashboard';
        }

        return redirect()
            ->route($destination)
            ->with(
                'success',
                "Switched to {$site->name}."
            );
    }
}
