<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        if (! Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = Auth::guard('admin')->user();

        if (! $admin->status) {
            Auth::guard('admin')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->withErrors([
                    'email' => 'Your admin account is inactive.',
                ]);
        }

        if ($admin->isMemberManager()) {
            abort_unless($request->routeIs(
                'admin.dashboard', 'admin.members.*', 'admin.activities.*', 'admin.rotations.*',
                'admin.delete-profile-requests.*', 'admin.site.*',
                'admin.settings.password.*', 'admin.logout'
            ), 403, 'Member managers can only access member management.');
        }

        return $next($request);
    }
}
