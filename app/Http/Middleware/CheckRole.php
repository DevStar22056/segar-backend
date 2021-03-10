<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $userRole = $request->user();

        if ($userRole->role === "1") {
            return $next($request);
        } else {
            return abort(401);
        }
    }
}
