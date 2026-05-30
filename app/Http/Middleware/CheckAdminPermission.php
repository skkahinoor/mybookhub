<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\RoleHelper;

class CheckAdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        // 1. Ensure user is logged in as admin
        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated.'
                ], 401);
            }
            return redirect('/admin/login');
        }

        $user = Auth::guard('admin')->user();

        // 2. Bypass checks for superadmin and core admin roles
        if (
            $user->hasRole('admin') || 
            $user->hasRole('superadmin') || 
            (class_exists(RoleHelper::class) && $user->role_id == RoleHelper::adminId())
        ) {
            return $next($request);
        }

        // 3. Check Spatie permission for the staff/restricted user
        if (!$user->can($permission)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized action. You do not have permission to access this resource.'
                ], 403);
            }

            return redirect()->route('admin.dashboard')->with('error_message', 'You do not have permission to access that section.');
        }

        return $next($request);
    }
}
