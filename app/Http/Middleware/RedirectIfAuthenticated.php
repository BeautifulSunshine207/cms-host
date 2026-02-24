<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     * Check Role and see if already login
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
        public function handle(Request $request, Closure $next, string ...$guards): Response
        {
            $guards = empty($guards) ? [null] : $guards;

            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()) {
                    $user = Auth::guard($guard)->user();

                    return match ($user->role) {
                        'admin'   => redirect('/projects'),
                        'finance' => redirect('/projects'),
                        'pm'      => redirect('/projects'),
                        default   => redirect('/employee/projects/employeedashboard'),
                    };
                }
            }

            return $next($request);
        }
}