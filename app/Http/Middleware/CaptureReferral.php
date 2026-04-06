<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureReferral
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('ref')) {
            $referralCode = $request->query('ref');
            // Store referral code in cookie for 30 days
            \Illuminate\Support\Facades\Cookie::queue('referral_code', $referralCode, 43200);
        }

        return $next($request);
    }
}
