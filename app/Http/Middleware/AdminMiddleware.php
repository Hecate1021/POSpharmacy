<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next): Response
{
    // Check if user is logged in AND is an admin (role 1)
    if (auth()->check() && auth()->user()->role !== 1) {
        abort(403, 'Unauthorized Access');
    }

    return $next($request);
}
}
