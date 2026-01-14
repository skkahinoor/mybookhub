<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Vendor
{
    public function handle(Request $request, Closure $next)
    {
        // Require authentication via the admin guard
        if (! Auth::guard('admin')->check()) {
            // Use the existing admin login route
            return redirect()->route('admin.login');
        }

        // Only allow admins that are vendors
        if (Auth::guard('admin')->user()->type !== 'vendor') {
            abort(403, 'Vendor access only.');
        }

        return $next($request);
    }
}
