<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Authentication required to access this portal.');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized portal access for current user role.');
        }

        if ($user->status === 'suspended') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been suspended by market administrators.');
        }

        return $next($request);
    }
}
