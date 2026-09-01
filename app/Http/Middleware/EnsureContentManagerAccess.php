<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureContentManagerAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin || $admin->hasRole('super-admin') || ! $admin->hasRole('content-manager')) {
            return $next($request);
        }

        if (! $request->routeIs(
            'admin.blog-posts.*',
            'admin.success-stories.*',
            'admin.pages.*'
        )) {
            abort(403, 'Content managers can only access content management pages.');
        }

        return $next($request);
    }
}
