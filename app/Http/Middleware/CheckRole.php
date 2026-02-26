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
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Check if user is logged in and has the required role
        if (!$request->user() || $request->user()->role !== $role) {
            return redirect('/')->with('error', 'Unauthorized access.');
        }
        return $next($request);
    }
}
