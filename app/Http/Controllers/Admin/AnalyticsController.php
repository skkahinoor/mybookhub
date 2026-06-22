<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        Session::put('page', 'analytics');

        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();

        // --- Filters ---
        $dateFrom = $request->get('date_from', now()->subDays(29)->toDateString());
        $dateTo   = $request->get('date_to', now()->toDateString());
        $module   = $request->get('module', '');
        $country  = $request->get('country', '');

        $query = PageView::query()
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($module) {
            $query->where('module', $module);
        }
        if ($country) {
            $query->where('country', $country);
        }

        // --- Summary Stats ---
        $totalUniqueViews = (clone $query)->count();

        $topPage = (clone $query)
            ->select('page_title', DB::raw('COUNT(*) as views'))
            ->groupBy('page_title')
            ->orderByDesc('views')
            ->first();

        $topCountry = (clone $query)
            ->select('country', DB::raw('COUNT(*) as views'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('views')
            ->first();

        $topModule = (clone $query)
            ->select('module', DB::raw('COUNT(*) as views'))
            ->groupBy('module')
            ->orderByDesc('views')
            ->first();

        // --- Daily Trend (for chart) ---
        $dailyTrend = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as views'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill in missing dates with 0
        $trendLabels = [];
        $trendData   = [];
        $current = \Carbon\Carbon::parse($dateFrom);
        $end     = \Carbon\Carbon::parse($dateTo);
        while ($current->lte($end)) {
            $dateStr       = $current->toDateString();
            $trendLabels[] = $current->format('d M');
            $trendData[]   = $dailyTrend->has($dateStr) ? (int) $dailyTrend[$dateStr]->views : 0;
            $current->addDay();
        }

        // --- Module Breakdown (for chart) ---
        $moduleBreakdown = (clone $query)
            ->select('module', DB::raw('COUNT(*) as views'))
            ->groupBy('module')
            ->orderByDesc('views')
            ->get();

        // --- Top Pages (table) ---
        $topPages = (clone $query)
            ->select('url', 'page_title', 'module', DB::raw('COUNT(*) as unique_views'), DB::raw('COUNT(DISTINCT country) as countries'))
            ->groupBy('url', 'page_title', 'module')
            ->orderByDesc('unique_views')
            ->limit(50)
            ->get();

        // --- Country Breakdown ---
        $countryBreakdown = (clone $query)
            ->select('country', 'state', DB::raw('COUNT(*) as views'))
            ->whereNotNull('country')
            ->groupBy('country', 'state')
            ->orderByDesc('views')
            ->limit(100)
            ->get();

        // --- Filters meta ---
        $allModules   = PageView::select('module')->distinct()->pluck('module');
        $allCountries = PageView::select('country')->whereNotNull('country')->distinct()->orderBy('country')->pluck('country');

        return view('admin.reports.analytics', compact(
            'logos', 'headerLogo',
            'totalUniqueViews', 'topPage', 'topCountry', 'topModule',
            'trendLabels', 'trendData',
            'moduleBreakdown', 'topPages', 'countryBreakdown',
            'allModules', 'allCountries',
            'dateFrom', 'dateTo', 'module', 'country'
        ));
    }
}
