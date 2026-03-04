<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is logged in and has one of the required roles
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            return redirect('/')->with('error', 'Unauthorized access.');
        }
        return $next($request);
    }
}
