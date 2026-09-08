<?php

use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\AdminGuest;
use App\Http\Middleware\CheckAnyPermission;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureContentManagerAccess;
use App\Http\Middleware\EnsureRelationshipManagerMemberAccess;
use App\Http\Middleware\ResolveApplication;
use App\Http\Middleware\SetAdminSiteConnection;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => AdminAuthenticate::class,
            'admin.guest' => AdminGuest::class,
            'permission' => CheckPermission::class,
            'permission.any' => CheckAnyPermission::class,
            'role' => CheckRole::class,
            'content.manager' => EnsureContentManagerAccess::class,
            'relationship.manager.member' => EnsureRelationshipManagerMemberAccess::class,
            'admin.site' => SetAdminSiteConnection::class,
            'application' => ResolveApplication::class,
        ]);

        $middleware->prependToPriorityList(
            before: AuthenticatesRequests::class,
            prepend: ResolveApplication::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
