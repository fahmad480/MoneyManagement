<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check if email verification is required in config
        if (config('auth.email_verification_required')) {
            if (Auth::check() && is_null(Auth::user()->email_verified_at)) {
                // Allow access to verification routes
                if (!$request->routeIs('verification.*') && !$request->routeIs('logout')) {
                    return redirect()->route('verification.notice');
                }
            }
        }
        
        return $next($request);
    }
}

