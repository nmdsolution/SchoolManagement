<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Rawilk\Settings\Facades\Settings;
use Symfony\Component\HttpFoundation\Response;

class CenterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            if (get_center_id()) {
                Settings::setTeamId(get_center_id());
            }
        }

        return $next($request);
    }
}
