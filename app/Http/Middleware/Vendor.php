<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Vendor
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('vendor.login');

        if (Auth::guard('vendor')->user()->type !== 'vendor') {
            abort(403, 'Vendor access only.');
        }

        return $next($request);
    }
}
