<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route-level role gate. Registered as the "role" alias in bootstrap/app.php.
 *
 *   Route::middleware('role:midwife')->group(...);
 *   Route::middleware('role:midwife,health_worker')->group(...);
 *
 * This is a coarse "may this role see this section at all" check. Whether a
 * user may touch one specific record is a policy question -- see app/Policies.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user && in_array($user->role->value, $roles, true), 403);

        return $next($request);
    }
}
