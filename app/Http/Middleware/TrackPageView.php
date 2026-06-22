<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PageView;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrackPageView
{
    /**
     * Paths that should never be tracked.
     */
    protected array $excludedPrefixes = [
        'admin',
        'api',
        '_debugbar',
        'telescope',
        'horizon',
    ];

    protected array $excludedExtensions = [
        'css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'webp',
        'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'map',
        'pdf', 'zip', 'xlsx', 'csv',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only track GET requests with successful HTML responses
        if ($request->isMethod('GET') && !$request->ajax() && $this->shouldTrack($request)) {
            try {
                $this->recordView($request);
            } catch (\Exception $e) {
                Log::error('PageView tracking error: ' . $e->getMessage());
            }
        }

        return $response;
    }

    protected function shouldTrack(Request $request): bool
    {
        $path = ltrim($request->path(), '/');

        // Skip if the path starts with any excluded prefix
        foreach ($this->excludedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return false;
            }
        }

        // Skip asset file extensions
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext && in_array($ext, $this->excludedExtensions)) {
            return false;
        }

        // Skip favicon, robots.txt, sitemap
        if (in_array($path, ['favicon.ico', 'robots.txt', 'sitemap.xml', ''])) {
            return false;
        }

        return true;
    }

    protected function recordView(Request $request): void
    {
        $ip = $request->ip();
        $url = '/' . ltrim($request->path(), '/');
        $module = $this->detectModule($request);
        $pageTitle = $this->buildPageTitle($url, $module);

        // Check if this IP already visited this page (all-time unique)
        $alreadyViewed = PageView::where('url', $url)->where('ip_address', $ip)->exists();
        if ($alreadyViewed) {
            return;
        }

        // Get geolocation (cached per IP in session)
        $geo = $this->getGeo($request, $ip);
        $userAgent = $request->userAgent() ?? '';
        $device = $this->detectDevice($userAgent);

        PageView::create([
            'url'        => $url,
            'page_title' => $pageTitle,
            'module'     => $module,
            'ip_address' => $ip,
            'country'    => $geo['country'] ?? null,
            'state'      => $geo['state'] ?? null,
            'city'       => $geo['city'] ?? null,
            'user_agent' => substr($userAgent, 0, 500),
            'device'     => $device,
        ]);
    }

    protected function detectDevice(string $userAgent): string
    {
        $userAgentLower = strtolower($userAgent);
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $userAgentLower)) {
            return 'Tablet';
        }
        if (preg_match('/(up\.browser|up\.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile|iphone|ipod|blackberry|nokia|opera mini|mini|windows ce|webos|meego|palmsource|bb10)/i', $userAgentLower)) {
            return 'Mobile';
        }
        return 'Desktop';
    }

    protected function detectModule(Request $request): string
    {
        $path = $request->path();

        if (str_starts_with($path, 'student') || str_starts_with($path, 'user/student')) {
            return 'student';
        }
        if (str_starts_with($path, 'vendor')) {
            return 'vendor';
        }
        if (str_starts_with($path, 'sales-executive') || str_starts_with($path, 'sales')) {
            return 'sales';
        }

        return 'frontend';
    }

    protected function buildPageTitle(string $url, string $module): string
    {
        // Map common URL patterns to human-readable titles
        $patterns = [
            '/'                          => 'Home',
            '/products'                  => 'Products',
            '/cart'                      => 'Cart',
            '/checkout'                  => 'Checkout',
            '/login'                     => 'Login',
            '/register'                  => 'Register',
            '/profile'                   => 'Profile',
            '/book-requests'             => 'Book Requests',
            '/sell-book'                 => 'Sell Book',
            '/contact'                   => 'Contact',
            '/about'                     => 'About Us',
        ];

        if (isset($patterns[$url])) {
            return $patterns[$url];
        }

        // Dynamic title from URL segments
        $segments = array_filter(explode('/', trim($url, '/')));
        if (empty($segments)) {
            return 'Home';
        }

        $last = end($segments);
        // If last segment is numeric (ID), use second-to-last
        if (is_numeric($last) && count($segments) > 1) {
            $last = prev($segments);
        }

        return ucwords(str_replace(['-', '_'], ' ', $last));
    }

    protected function getGeo(Request $request, string $ip): array
    {
        // On localhost, skip geo lookup
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return ['country' => 'Local', 'state' => 'Local', 'city' => 'Local'];
        }

        // Cache per session to avoid repeated API calls
        $cacheKey = 'geo_' . md5($ip);
        if ($request->session()->has($cacheKey)) {
            return $request->session()->get($cacheKey);
        }

        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}", [
                'fields' => 'status,country,regionName,city',
            ]);

            $data = $response->json();

            if (($data['status'] ?? '') === 'success') {
                $geo = [
                    'country' => $data['country'] ?? null,
                    'state'   => $data['regionName'] ?? null,
                    'city'    => $data['city'] ?? null,
                ];
            } else {
                $geo = ['country' => null, 'state' => null, 'city' => null];
            }
        } catch (\Exception $e) {
            $geo = ['country' => null, 'state' => null, 'city' => null];
        }

        // Cache for this session
        $request->session()->put($cacheKey, $geo);

        return $geo;
    }
}
