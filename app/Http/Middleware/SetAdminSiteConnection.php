<?php

namespace App\Http\Middleware;

use App\Models\Site;
use App\Services\SiteDatabaseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAdminSiteConnection
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        /*
        |--------------------------------------------------------------------------
        | Get selected site ID
        |--------------------------------------------------------------------------
        */

        $siteId = session('admin_site_id');

        /*
        |--------------------------------------------------------------------------
        | No site selected
        |--------------------------------------------------------------------------
        */

        if (! $siteId) {
            return redirect()
                ->route('admin.site.select');
        }

        /*
        |--------------------------------------------------------------------------
        | Find selected site
        |--------------------------------------------------------------------------
        */

        $site = Site::find($siteId);

        if (! $site) {

            session()->forget('admin_site_id');

            return redirect()
                ->route('admin.site.select')
                ->withErrors([
                    'site' => 'Selected site no longer exists.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check site status
        |--------------------------------------------------------------------------
        */

        if (! $site->status) {

            session()->forget('admin_site_id');

            return redirect()
                ->route('admin.site.select')
                ->withErrors([
                    'site' => 'Selected site is inactive.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Connect to selected site's database
        |--------------------------------------------------------------------------
        */

        if ($request->user('admin')?->isMemberManager()) {
            abort_unless($request->user('admin')->hasSiteAccess($site->id), 403, 'You do not have access to this site.');
        }

        app(SiteDatabaseService::class)->connect($site);

        /*
        |--------------------------------------------------------------------------
        | Continue request
        |--------------------------------------------------------------------------
        */

        return $next($request);
    }
}
