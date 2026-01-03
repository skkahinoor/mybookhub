<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\InstitutionManagement;
use App\Models\SalesExecutive;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SalesReportController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\View\View
    {
        Session::put('page', 'report_management');

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        // Distinct options for filters (by name text)
        $countries = SalesExecutive::whereNotNull('country')->where('country', '!=', '')->select('country as name')->distinct()->get();
        $states = SalesExecutive::whereNotNull('state')->where('state', '!=', '')->select('state as name')->distinct()->get();
        $districts = SalesExecutive::whereNotNull('district')->where('district', '!=', '')->select('district as name')->distinct()->get();
        $blocks = SalesExecutive::whereNotNull('block')->where('block', '!=', '')->select('block as name')->distinct()->get();

        $query = SalesExecutive::query();

        if ($request->filled('country_id')) {
            $query->where('country', $request->get('country_id'));
        }

        if ($request->filled('state_id')) {
            $query->where('state', $request->get('state_id'));
        }

        if ($request->filled('district_id')) {
            $query->where('district', $request->get('district_id'));
        }

        if ($request->filled('block_id')) {
            $query->where('block', $request->get('block_id'));
        }

        // Global search
        $search = $request->input('search');
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('country', 'like', '%' . $search . '%')
                    ->orWhere('state', 'like', '%' . $search . '%')
                    ->orWhere('district', 'like', '%' . $search . '%')
                    ->orWhere('block', 'like', '%' . $search . '%');
            });
        }

        $salesExecutives = $query->orderBy('name')->get();

        return view('admin.reports.sales_reports.index', compact(
            'countries',
            'states',
            'districts',
            'blocks',
            'salesExecutives',
            'logos',
            'headerLogo'
        ));
    }

    public function show($id)
    {
        Session::put('page', 'report_management');

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        $salesExecutive = SalesExecutive::findOrFail($id);

        // Institutions added by this sales executive
        $institutions = InstitutionManagement::with(['country', 'state', 'district', 'block'])
            ->where('added_by', $salesExecutive->id)
            ->get();

        // Students added by this sales executive
        $students = User::with(['institution', 'country', 'state', 'district', 'block'])
            ->where('added_by', $salesExecutive->id)
            ->get();

        // Report stats (same logic as sales-side report, but for this specific executive)
        $incomePerTarget = $salesExecutive->income_per_target ?? 0;

        $approvedStudents = User::where('added_by', $salesExecutive->id)
            ->where('status', 1);

        $todayStudentsCount = (clone $approvedStudents)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $weeklyStudentsCount = (clone $approvedStudents)
            ->whereDate('created_at', '>=', Carbon::now()->startOfWeek())
            ->count();

        $monthlyStudentsCount = (clone $approvedStudents)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $totalStudentsCount = (clone $approvedStudents)->count();

        $todayEarning = $incomePerTarget * $todayStudentsCount;
        $weeklyEarning = $incomePerTarget * $weeklyStudentsCount;
        $monthlyEarning = $incomePerTarget * $monthlyStudentsCount;
        $totalEarning = $incomePerTarget * $totalStudentsCount;

        // Graph data for last 30 days
        $days = 30;
        $startDate = now()->subDays($days - 1)->startOfDay();

        $dates = [];
        $dateKeys = [];
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($days - 1 - $i);
            $dates[] = $date->format('d M');
            $dateKeys[] = $date->format('Y-m-d');
        }

        $studentData = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('added_by', $salesExecutive->id)
            ->where('status', 1)
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $studentsCount = [];
        foreach ($dateKeys as $dateKey) {
            $studentsCount[] = $studentData[$dateKey] ?? 0;
        }

        $earningsData = [];
        foreach ($studentsCount as $count) {
            $earningsData[] = $count * $incomePerTarget;
        }

        return view('admin.reports.sales_reports.view', compact(
            'salesExecutive',
            'institutions',
            'students',
            'todayEarning',
            'weeklyEarning',
            'monthlyEarning',
            'totalEarning',
            'todayStudentsCount',
            'weeklyStudentsCount',
            'monthlyStudentsCount',
            'totalStudentsCount',
            'incomePerTarget',
            'dates',
            'studentsCount',
            'earningsData',
            'logos',
            'headerLogo'
        ));
    }
}

