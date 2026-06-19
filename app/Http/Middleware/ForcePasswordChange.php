<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Routes that should be accessible even when password change is required
        $excludedRoutes = [
            'password.change',
            'password.change.update',
            'password.update',
            'logout',
            'two-factor.*',
        ];

        // Check if current route is in excluded list
        foreach ($excludedRoutes as $route) {
            if ($request->routeIs($route)) {
                return $next($request);
            }
        }

        // Check if user is authenticated and needs to change password
        if (auth()->check()) {
            $user = auth()->user();

            if ($user instanceof User && $user->needsPasswordChange()) {
                // If not already on password change page, redirect there
                if (!$request->routeIs('password.change')) {
                    return redirect()->route('password.change')
                        ->with('warning', 'You must change your password before continuing.');
                }
            }
        }

        return $next($request);
    }
}
