<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectByRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($request->routeIs('dashboard')) {
            return redirect()->route(match ($user->role->value) {
                'midwife' => 'midwife.dashboard',
                'patient' => 'patient.dashboard',
                default => 'dashboard',
            });
        }

        return $next($request);
    }
}
