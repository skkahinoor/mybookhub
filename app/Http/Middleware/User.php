<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class User
{
    /**
     * Handle an incoming request.
     *
     * Ensure the visitor is an authenticated front-end user
     * and redirect to the user login page if not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If not logged in on the default/web guard, send to user login
        if (!Auth::check()) {
            return redirect()->route('user.login');
        }

        // Optionally ensure this account is a normal 'user'
        $authUser = Auth::user();
        if ($authUser->type !== 'user') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('user.login')
                ->with('error', 'You are not authorized to access the user area.');
        }

        return $next($request);
    }
}
