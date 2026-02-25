<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VolunteerTeacherMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || $user->role !== User::ROLE_VOLUNTEER_TEACHER) {
            abort(403, 'Volunteer teacher access only.');
        }

        return $next($request);
    }
}

