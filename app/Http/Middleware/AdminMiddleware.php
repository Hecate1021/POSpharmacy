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
    // Allow if user is 'admin' OR 'branch_manager'
    if (auth()->check() && in_array(auth()->user()->role, ['admin', 'branch_manager'])) {
        return $next($request);
    }

    abort(403, 'Unauthorized Access');
}
}
