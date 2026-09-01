<?php

namespace App\Http\Middleware;

use App\Models\DeleteProfileRequest;
use App\Services\RelationshipManagerAccess;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRelationshipManagerMemberAccess
{
    public function __construct(
        private readonly RelationshipManagerAccess $access
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->access->isRestricted()) {
            return $next($request);
        }

        if (! $request->routeIs(
            'admin.dashboard',
            'admin.members.*',
            'admin.activities.*',
            'admin.rotations.*',
            'admin.delete-profile-requests.*'
        )) {
            abort(403, 'Relationship managers can only access member management pages.');
        }

        if ($request->routeIs('admin.members.relationship-manager.update')) {
            abort(403, 'Relationship managers cannot reassign members.');
        }

        $memberId = $this->memberId($request);

        if ($memberId !== null && ! $this->access->canAccessMember($memberId)) {
            abort(403, 'You can only access members assigned to you.');
        }

        return $next($request);
    }

    private function memberId(Request $request): ?int
    {
        if ($request->routeIs('admin.delete-profile-requests.accept', 'admin.delete-profile-requests.reject')) {
            return DeleteProfileRequest::query()
                ->whereKey((int) $request->route('id'))
                ->value('user_id');
        }

        if (! $request->routeIs('admin.members.*', 'admin.activities.member')) {
            return null;
        }

        foreach (['memberId', 'member', 'id'] as $parameter) {
            $value = $request->route($parameter);

            if ($value instanceof Model) {
                return (int) $value->getKey();
            }

            if (is_numeric($value)) {
                return (int) $value;
            }
        }

        return null;
    }
}
