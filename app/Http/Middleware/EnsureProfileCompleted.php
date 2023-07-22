<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'admin') {
                // Allow admin users to access the route without profile completion check
                return $next($request);
            }

            if (!$user->userProfiles) {
                return response()->json([
                    'message' => 'Please complete your user profile first.'
                ], 403);
            }
        }

        return $next($request);
    }
}
