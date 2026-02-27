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
        // If not logged in on the default/web guard, send to student login
        if (! Auth::check()) {
            return redirect()->route('student.login');
        }

        // Ensure this account is a front-facing student (legacy "user" is treated as student)
        $authUser = Auth::user();
        if (! in_array($authUser->type, ['student', 'user'], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('student.login')
                ->with('error', 'You are not authorized to access the student area.');
        }

        return $next($request);
    }
}
