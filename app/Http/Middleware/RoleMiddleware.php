<?php

namespace App\Http\Middleware;

use App\Traits\Base;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    use Base;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        $roles = explode("|", $roles);

        if (!auth()->check()) {
            return Base::error("You are not logged in!");
            // return route('login');
        }

        // if (!auth()->user()->is_verified) {
        //     return Base::error("Please verify your account!", [], "not_verified");
        // }

        if (in_array(auth()->user()->role, $roles)) {
            // return $next($request);
            $response = $next($request);
            // Always return a response
            return $response ?: new Response();
        }

        return Base::error("Unauthorized!");
        // return redirect()->route('login');

        return $next($request);
    }
}
