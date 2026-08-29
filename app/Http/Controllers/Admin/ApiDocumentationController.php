<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class ApiDocumentationController extends Controller
{
    public function index(): View
    {
        $endpoints = collect(Route::getRoutes()->getRoutes())
            ->filter(fn (LaravelRoute $route): bool => str_starts_with($route->uri(), 'api/'))
            ->map(fn (LaravelRoute $route): array => $this->documentRoute($route))
            ->sortBy(fn (array $endpoint): string => $endpoint['group'].'/'.$endpoint['uri'])
            ->values();

        return view('admin.api-documentation.index', [
            'endpointGroups' => $endpoints->groupBy('group'),
            'endpointCount' => $endpoints->count(),
            'authenticatedCount' => $endpoints->where('authenticated', true)->count(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function documentRoute(LaravelRoute $route): array
    {
        $methods = collect($route->methods())->reject(fn (string $method): bool => $method === 'HEAD');
        $middleware = collect($route->gatherMiddleware())->values();
        $action = $route->getActionName();
        $feature = explode('/', preg_replace('#^api/v\d+/#', '', $route->uri()))[0] ?? 'general';

        return [
            'methods' => $methods,
            'uri' => '/'.$route->uri(),
            'name' => $route->getName(),
            'action' => $action === 'Closure' ? 'Inline handler' : class_basename($action),
            'middleware' => $middleware,
            'authenticated' => $middleware->contains('auth:sanctum'),
            'group' => str($feature)->replace('-', ' ')->title()->toString(),
            'parameters' => $this->parameters($route->uri()),
        ];
    }

    /**
     * @return Collection<int, string>
     */
    private function parameters(string $uri): Collection
    {
        preg_match_all('/\{([^}]+)}/', $uri, $matches);

        return collect($matches[1] ?? []);
    }
}
