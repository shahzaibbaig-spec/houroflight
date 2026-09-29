<?php
namespace Nsms\Kcat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string ...$permissions)
    {
        abort_unless($request->user(), 401);
        $allowed = collect($permissions)->flatMap(fn ($permission) => preg_split('/[|,]/', $permission))
            ->contains(fn ($permission) => $request->user()->can('kcat.'.$permission));
        abort_unless($allowed, 403);
        return $next($request);
    }
}
