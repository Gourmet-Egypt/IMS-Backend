<?php

namespace App\Http\Middleware;

use App\Traits\Responses;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    use Responses;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!auth()->check()) {
            return $this->error(401, 'Unauthenticated.');
        }

        if (!auth()->user() instanceof \App\Models\Admin) {
            return $this->error(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
