<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfBanned
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'banned') {
            return $next($request);
        }

        if ($request->routeIs('banned.appeal', 'banned.appeal.submit') || $request->is('logout')) {
            return $next($request);
        }

        return redirect()->route('banned.appeal');
    }
}
