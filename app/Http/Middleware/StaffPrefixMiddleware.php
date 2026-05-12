<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\RoleHelper;

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
        $uri = $request->getRequestUri();

        // Check if user is authenticated under the 'admin' guard
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();

            // Detect if the logged-in user is a custom-role staff member
            $coreRoles = ['admin', 'superadmin', 'vendor', 'sales', 'student', 'user'];
            $userRoleNames = $user->roles->pluck('name')->toArray();
            $isStaff = !empty(array_diff($userRoleNames, $coreRoles)) && !$user->hasRole('admin') && !$user->hasRole('superadmin');

            if ($isStaff) {
                // If a restricted staff member accesses '/admin/...' directly, redirect them to '/staff/...'
                if (strpos($uri, '/admin/') !== false) {
                    $newUri = str_replace('/admin/', '/staff/', $uri);
                    return redirect($newUri);
                }
            } else {
                // If a superadmin/admin accesses '/staff/...' directly, redirect them to '/admin/...'
                if (strpos($uri, '/staff/') !== false) {
                    $newUri = str_replace('/staff/', '/admin/', $uri);
                    return redirect($newUri);
                }
            }
        }

        // Process request
        $response = $next($request);

        // Modify response for authenticated users to ensure URL consistency in links and redirects
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $coreRoles = ['admin', 'superadmin', 'vendor', 'sales', 'student', 'user'];
            $userRoleNames = $user->roles->pluck('name')->toArray();
            $isStaff = !empty(array_diff($userRoleNames, $coreRoles)) && !$user->hasRole('admin') && !$user->hasRole('superadmin');

            if ($isStaff) {
                // For restricted staff, rewrite redirect Location headers to '/staff/'
                if ($response->isRedirection()) {
                    $location = $response->headers->get('Location');
                    if ($location && strpos($location, '/admin/') !== false) {
                        $newLocation = str_replace('/admin/', '/staff/', $location);
                        $response->headers->set('Location', $newLocation);
                    }
                }

                // For restricted staff, rewrite HTML links to '/staff/'
                $contentType = $response->headers->get('Content-Type');
                if ($contentType && strpos($contentType, 'text/html') !== false) {
                    $content = $response->getContent();
                    if (is_string($content)) {
                        $content = $this->rewriteAdminHtml($content, '/admin/', '/staff/');
                        $response->setContent($content);
                    }
                }
            } else {
                // For admins/superadmins, rewrite redirect Location headers to '/admin/' if they point to '/staff/'
                if ($response->isRedirection()) {
                    $location = $response->headers->get('Location');
                    if ($location && strpos($location, '/staff/') !== false) {
                        $newLocation = str_replace('/staff/', '/admin/', $location);
                        $response->headers->set('Location', $newLocation);
                    }
                }

                // For admins/superadmins, rewrite HTML links to '/admin/' if they point to '/staff/'
                $contentType = $response->headers->get('Content-Type');
                if ($contentType && strpos($contentType, 'text/html') !== false) {
                    $content = $response->getContent();
                    if (is_string($content)) {
                        $content = $this->rewriteAdminHtml($content, '/staff/', '/admin/');
                        $response->setContent($content);
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Helper to rewrite prefix in HTML attributes, excluding static assets.
     */
    private function rewriteAdminHtml($html, string $from, string $to)
    {
        $escapedFrom = preg_quote($from, '/');
        $pattern = '/(href|action)=["\']([^"\']*)(' . $escapedFrom . ')([^"\']*)(["\'])/i';
        
        $rewritten = preg_replace_callback($pattern, function($matches) use ($from, $to) {
            $attribute = $matches[1];
            $base = $matches[2];
            $path = $matches[4];
            $quote = $matches[5];
            
            $extensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'map'];
            $pathInfo = pathinfo($path);
            $extension = isset($pathInfo['extension']) ? strtolower($pathInfo['extension']) : '';
            
            if (in_array($extension, $extensions)) {
                return $attribute . '=' . $quote . $base . $from . $path . $quote;
            }
            
            return $attribute . '=' . $quote . $base . $to . $path . $quote;
        }, $html);

        // Also handle JSON endpoints, script tags or other instances of "/admin/" or "/staff/"
        $rewritten = str_replace('"' . $from, '"' . $to, $rewritten);
        $rewritten = str_replace('\'' . $from, '\'' . $to, $rewritten);

        return $rewritten;
    }
}
