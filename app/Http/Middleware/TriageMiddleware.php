<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TriageMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! $user->isTriage() && ! $user->isTriageDoctor() && ! $user->isWardDoctor() && ! $user->isAdmin())) {
            abort(403, 'Triage access required.');
        }

        if ($user->isWardDoctor()) {
            $allowedRoutes = [
                'dashboard',
                'search',
                'white.board',
                'overview.dashboard',
                'patients.index',
                'patients.show',
            ];

            $routeName = $request->route()?->getName();

            if (! in_array($routeName, $allowedRoutes, true)) {
                abort(403, 'Read-only ward doctor access only.');
            }
        }

        return $next($request);
    }
}
