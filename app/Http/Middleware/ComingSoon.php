<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class ComingSoon
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if maintenance mode is enabled (priority over coming soon)
        $maintenanceModeEnabled = Setting::getValue('maintenance_mode_enabled', 0);
        
        // Check if coming soon mode is enabled
        $comingSoonEnabled = Setting::getValue('coming_soon_enabled', 0);
        
        // Allow admin routes to bypass both modes
        if ($request->is('admin/*') || $request->is('vendor/*') || $request->is('user/*') || $request->is('sales/*')) {
            return $next($request);
        }
        
        // If maintenance mode is enabled, show maintenance page
        if ($maintenanceModeEnabled == 1) {
            try {
                $logos = \App\Models\HeaderLogo::first();
            } catch (\Exception $e) {
                $logos = null;
            }
            
            // Get social media URLs
            $socialMedia = [
                'facebook' => Setting::getValue('social_facebook', ''),
                'twitter' => Setting::getValue('social_twitter', ''),
                'instagram' => Setting::getValue('social_instagram', ''),
                'linkedin' => Setting::getValue('social_linkedin', ''),
                'youtube' => Setting::getValue('social_youtube', ''),
                'pinterest' => Setting::getValue('social_pinterest', ''),
                'whatsapp' => Setting::getValue('social_whatsapp', ''),
                'telegram' => Setting::getValue('social_telegram', ''),
            ];
            
            return response()->view('front.maintenance', compact('logos', 'socialMedia'));
        }
        
        // If coming soon is enabled, show coming soon page
        if ($comingSoonEnabled == 1) {
            try {
                $logos = \App\Models\HeaderLogo::first();
            } catch (\Exception $e) {
                $logos = null;
            }
            
            // Get countdown settings
            $showCountdown = Setting::getValue('show_countdown', 1);
            $countdownDate = Setting::getValue('countdown_date', '');
            $countdownTime = Setting::getValue('countdown_time', '');
            
            // Get social media URLs
            $socialMedia = [
                'facebook' => Setting::getValue('social_facebook', ''),
                'twitter' => Setting::getValue('social_twitter', ''),
                'instagram' => Setting::getValue('social_instagram', ''),
                'linkedin' => Setting::getValue('social_linkedin', ''),
                'youtube' => Setting::getValue('social_youtube', ''),
                'pinterest' => Setting::getValue('social_pinterest', ''),
                'whatsapp' => Setting::getValue('social_whatsapp', ''),
                'telegram' => Setting::getValue('social_telegram', ''),
            ];
            
            return response()->view('front.coming-soon', compact(
                'logos',
                'showCountdown',
                'countdownDate',
                'countdownTime',
                'socialMedia'
            ));
        }
        
        return $next($request);
    }
}
