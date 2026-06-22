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
        $topPages = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))
            ->mergeBindings($subQuery)
            ->orderByDesc('unique_views')
            ->paginate(10)
            ->appends($request->all());

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

        // --- Peak Active Hours ---
        $peakHours = (clone $query)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as views'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('views', 'hour')
            ->toArray();
            
        $peakHoursData = [];
        for ($i = 0; $i < 24; $i++) {
            $peakHoursData[] = $peakHours[$i] ?? 0;
        }

        // --- Global Device Breakdown ---
        $deviceBreakdown = (clone $query)
            ->select(
                DB::raw('SUM(CASE WHEN device = \'Desktop\' OR device IS NULL THEN 1 ELSE 0 END) as desktop_views'),
                DB::raw('SUM(CASE WHEN device = \'Mobile\' THEN 1 ELSE 0 END) as mobile_views'),
                DB::raw('SUM(CASE WHEN device = \'Tablet\' THEN 1 ELSE 0 END) as tablet_views')
            )->first();
            
        // --- Top Cities ---
        $topCities = (clone $query)
            ->select('city', 'country', DB::raw('COUNT(*) as views'))
            ->whereNotNull('city')
            ->groupBy('city', 'country')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        return view('admin.reports.analytics', compact(
            'logos', 'headerLogo',
            'totalUniqueViews', 'topPage', 'topCountry', 'topModule',
            'trendLabels', 'trendData',
            'moduleBreakdown', 'topPages', 'countryBreakdown',
            'allModules', 'allCountries',
            'dateFrom', 'dateTo', 'module', 'country',
            'peakHoursData', 'deviceBreakdown', 'topCities'
        ));
    }

    public function exportCsv(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(29)->toDateString());
        $dateTo   = $request->get('date_to', now()->toDateString());
        $module   = $request->get('module', '');
        $country  = $request->get('country', '');

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

        $topPages = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))
            ->mergeBindings($subQuery)
            ->orderByDesc('unique_views')
            ->get();

        $filename = "analytics_export_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($topPages) {
            $file = fopen('php://output', 'w');
            // CSV Header
            fputcsv($file, ['Page Title', 'URL', 'Module', 'Unique Views', 'Countries Count', 'Desktop Views', 'Mobile Views', 'Tablet Views']);

            foreach ($topPages as $page) {
                fputcsv($file, [
                    $page->page_title,
                    $page->url,
                    $page->module,
                    $page->unique_views,
                    $page->countries,
                    $page->desktop_views,
                    $page->mobile_views,
                    $page->tablet_views
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(29)->toDateString());
        $dateTo   = $request->get('date_to', now()->toDateString());
        $module   = $request->get('module', '');
        $country  = $request->get('country', '');

        // Fetch same data as CSV
        $subQuery = DB::table('page_views')
            ->select(
                'url',
                'page_title',
                'module',
                DB::raw('COUNT(*) as unique_views'),
                DB::raw('COUNT(DISTINCT country) as countries')
            )
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($module) {
            $subQuery->where('module', $module);
        }
        if ($country) {
            $subQuery->where('country', $country);
        }

        $subQuery->groupBy('url', 'page_title', 'module');

        $topPages = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))
            ->mergeBindings($subQuery)
            ->orderByDesc('unique_views')
            ->limit(50) // Limit to top 50 for PDF to keep it readable
            ->get();
            
        $totalUniqueViews = DB::table('page_views')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->when($module, fn($q) => $q->where('module', $module))
            ->when($country, fn($q) => $q->where('country', $country))
            ->count();

        $data = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'topPages' => $topPages,
            'totalUniqueViews' => $totalUniqueViews
        ];

        $html = view('admin.reports.analytics_pdf', $data)->render();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream("analytics_report_" . date('Ymd_His') . ".pdf", ["Attachment" => true]);
    }
}
