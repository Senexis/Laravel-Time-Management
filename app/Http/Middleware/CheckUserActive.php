<?php

namespace App\Http\Middleware;

use Closure;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if ($request->user() && !$request->user()->is_active) {
                abort(423, 'User locked by an administrator.');
            }
        } catch (\Throwable $th) {
            abort(423, 'User locked by an administrator.');
        }

        return $next($request);
    }
}
