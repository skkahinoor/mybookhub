<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffPrefixMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check authenticated admin user
        if (Auth::guard('admin')->check()) {

            $user = Auth::guard('admin')->user();

            // Core roles
            $coreRoles = [
                'admin',
                'superadmin',
                'vendor',
                'sales',
                'student',
                'user'
            ];

            $userRoleNames = $user->roles->pluck('name')->toArray();

            // Detect restricted staff
            $isStaff =
                !empty(array_diff($userRoleNames, $coreRoles)) &&
                !$user->hasRole('admin') &&
                !$user->hasRole('superadmin');

            $path = trim($request->path(), '/');

            /*
            |--------------------------------------------------------------------------
            | STAFF USERS
            |--------------------------------------------------------------------------
            */

            if ($isStaff) {

                // Redirect admin/* => staff/*
                if (
    strpos($path, 'admin/') === 0 &&
    strpos($path, 'admin/orders/') !== 0
) {

    $newPath = '/staff/' .
        ltrim(substr($path, strlen('admin/')), '/');

    return redirect($newPath);
}

                // Redirect /admin => /staff
                if ($path === 'admin') {

                    return redirect('/staff');
                }
            }

            /*
            |--------------------------------------------------------------------------
            | ADMIN / SUPERADMIN USERS
            |--------------------------------------------------------------------------
            */

            else {

                // Redirect staff/* => admin/*
                if (strpos($path, 'staff/') === 0) {

                    $newPath = '/admin/' .
                        ltrim(substr($path, strlen('staff/')), '/');

                    return redirect($newPath);
                }

                // Redirect /staff => /admin
                if ($path === 'staff') {

                    return redirect('/admin');
                }
            }
        }

        // Continue request normally
        return $next($request);
    }
}