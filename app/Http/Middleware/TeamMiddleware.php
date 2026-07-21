<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! $user->isSpecialtyDoctor() && ! $user->isAdmin())) {
            abort(403, 'Specialty doctor access required.');
        }

        return $next($request);
    }
}
