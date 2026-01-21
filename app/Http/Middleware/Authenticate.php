<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // If admin routes → admin login
        if ($request->is('admin/*')) {
            return route('admin.login');
        }

        // If vendor routes → vendor login
        if ($request->is('vendor/*')) {
            return route('vendor.login');
        }

        // Default fallback
        return route('admin.login');
    }
}
