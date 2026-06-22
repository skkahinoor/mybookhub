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

        if ($request->ajax()) {
            $subQuery = DB::table('page_views')
                ->select(
                    'url',
                    'page_title',
                    'module',
                    DB::raw('COUNT(*) as unique_views'),
                    DB::raw('COUNT(DISTINCT country) as countries'),
                    DB::raw('SUM(CASE WHEN device = \'Desktop\' OR device IS NULL THEN 1 ELSE 0 END) as desktop_views'),
                    DB::raw('SUM(CASE WHEN device = \'Mobile\' THEN 1 ELSE 0 END) as mobile_views'),
                    DB::raw('SUM(CASE WHEN device = \'Tablet\' THEN 1 ELSE 0 END) as tablet_views')
                )
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

            if ($module) {
                $subQuery->where('module', $module);
            }
            if ($country) {
                $subQuery->where('country', $country);
            }

            $subQuery->groupBy('url', 'page_title', 'module');

            $topPagesQuery = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))
                ->mergeBindings($subQuery);

            return \Yajra\DataTables\Facades\DataTables::of($topPagesQuery)
                ->addIndexColumn()
                ->addColumn('device_breakdown', function ($row) {
                    $desktop = (int) $row->desktop_views;
                    $mobile = (int) $row->mobile_views;
                    $tablet = (int) $row->tablet_views;
                    $total = $desktop + $mobile + $tablet;

                    if ($total == 0) {
                        return '<span class="text-muted">None</span>';
                    }

                    $desktopPercent = $total > 0 ? round(($desktop / $total) * 100) : 0;
                    $mobilePercent = $total > 0 ? round(($mobile / $total) * 100) : 0;
                    $tabletPercent = $total > 0 ? round(($tablet / $total) * 100) : 0;

                    $html = '<div class="d-flex flex-column gap-1" style="font-size: 0.8rem; min-width: 140px; text-align: left; line-height: 1.4;">';
                    
                    if ($desktop > 0) {
                        $html .= '<div class="d-flex align-items-center justify-content-between text-secondary">
                                    <span><i class="fas fa-desktop text-primary me-1" style="width: 14px;"></i> Desktop</span>
                                    <span class="fw-bold ms-2">' . number_format($desktop) . ' (' . $desktopPercent . '%)</span>
                                  </div>';
                    }
                    if ($mobile > 0) {
                        $html .= '<div class="d-flex align-items-center justify-content-between text-secondary">
                                    <span><i class="fas fa-mobile-alt text-success me-1" style="width: 14px;"></i> Mobile</span>
                                    <span class="fw-bold ms-2">' . number_format($mobile) . ' (' . $mobilePercent . '%)</span>
                                  </div>';
                    }
                    if ($tablet > 0) {
                        $html .= '<div class="d-flex align-items-center justify-content-between text-secondary">
                                    <span><i class="fas fa-tablet-alt text-warning me-1" style="width: 14px;"></i> Tablet</span>
                                    <span class="fw-bold ms-2">' . number_format($tablet) . ' (' . $tabletPercent . '%)</span>
                                  </div>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('module_badge', function ($row) {
                    $colors = [
                        'frontend' => 'mod-badge frontend',
                        'student' => 'mod-badge student',
                        'vendor' => 'mod-badge vendor',
                        'sales' => 'mod-badge sales',
                    ];
                    $cls = $colors[strtolower($row->module)] ?? 'mod-badge other';
                    return '<span class="' . $cls . '">' . ucfirst($row->module) . '</span>';
                })
                ->rawColumns(['device_breakdown', 'module_badge'])
                ->make(true);
        }

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
        $topPages = collect(); // Loaded via AJAX DataTables

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
