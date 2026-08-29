<?php

namespace App\Http\Middleware;

use App\Models\Site;
use App\Services\SiteDatabaseService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentSite
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $admin = Auth::guard('admin')->user();

        /*
        |--------------------------------------------------------------------------
        | Admin must be authenticated
        |--------------------------------------------------------------------------
        */

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        /*
        |--------------------------------------------------------------------------
        | Get Selected Site
        |--------------------------------------------------------------------------
        */

        $siteId = session('admin_site_id');

        /*
        |--------------------------------------------------------------------------
        | No Site Selected
        |--------------------------------------------------------------------------
        */

        if (! $siteId) {
            return redirect()->route('admin.site.select');
        }

        /*
        |--------------------------------------------------------------------------
        | Get Site
        |--------------------------------------------------------------------------
        */

        $site = Site::find($siteId);

        if (! $site) {

            session()->forget('admin_site_id');

            return redirect()
                ->route('admin.site.select')
                ->withErrors([
                    'site' => 'Selected site does not exist.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Access
        |--------------------------------------------------------------------------
        */

        if (! $admin->hasSiteAccess($site->id)) {

            session()->forget('admin_site_id');

            return redirect()
                ->route('admin.site.select')
                ->withErrors([
                    'site' => 'You do not have access to this site.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Status
        |--------------------------------------------------------------------------
        */

        if (! $site->status) {

            session()->forget('admin_site_id');

            return redirect()
                ->route('admin.site.select')
                ->withErrors([
                    'site' => 'This site is currently inactive.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Connect To Site Database
        |--------------------------------------------------------------------------
        */

        app(SiteDatabaseService::class)
            ->connect($site);

        /*
        |--------------------------------------------------------------------------
        | Share Site With Views
        |--------------------------------------------------------------------------
        */

        view()->share(
            'currentSite',
            $site
        );

        return $next($request);
    }
}
