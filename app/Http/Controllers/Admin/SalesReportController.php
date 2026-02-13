<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\Block;
use App\Models\HeaderLogo;
use App\Models\InstitutionManagement;
use App\Models\SalesExecutive;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SalesReportController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\View\View
    {
        if (!Auth::guard('admin')->user()->can('view_reports')) {
            abort(403, 'Unauthorized action.');
        }
        Session::put('page', 'report_management');

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        // Distinct options for filters (from related Users)
        $countries = Country::select('countries.id', 'countries.name')
            ->join('users', 'users.country_id', '=', 'countries.id')
            ->join('sales_executives', 'sales_executives.user_id', '=', 'users.id')
            ->distinct()
            ->orderBy('countries.name')
            ->get();

        $states = State::select('states.id', 'states.name')
            ->join('users', 'users.state_id', '=', 'states.id')
            ->join('sales_executives', 'sales_executives.user_id', '=', 'users.id')
            ->distinct()
            ->orderBy('states.name')
            ->get();

        $districts = District::select('districts.id', 'districts.name')
            ->join('users', 'users.district_id', '=', 'districts.id')
            ->join('sales_executives', 'sales_executives.user_id', '=', 'users.id')
            ->distinct()
            ->orderBy('districts.name')
            ->get();

        $blocks = Block::select('blocks.id', 'blocks.name')
            ->join('users', 'users.block_id', '=', 'blocks.id')
            ->join('sales_executives', 'sales_executives.user_id', '=', 'users.id')
            ->distinct()
            ->orderBy('blocks.name')
            ->get();

        $query = SalesExecutive::query()
            ->select('sales_executives.*', 'users.name', 'users.phone')
            ->join('users', 'sales_executives.user_id', '=', 'users.id');

        if ($request->filled('country_id')) {
            $query->where('users.country_id', $request->get('country_id'));
        }

        if ($request->filled('state_id')) {
            $query->where('users.state_id', $request->get('state_id'));
        }

        if ($request->filled('district_id')) {
            $query->where('users.district_id', $request->get('district_id'));
        }

        if ($request->filled('block_id')) {
            $query->where('users.block_id', $request->get('block_id'));
        }

        // Global search
        $search = $request->input('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                // Name and phone have been moved to users table
                $q->where('users.name', 'like', '%' . $search . '%')
                    ->orWhere('users.phone', 'like', '%' . $search . '%');
            });
        }

        $salesExecutives = $query->orderBy('users.name')->get();

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
        if (!Auth::guard('admin')->user()->can('view_reports')) {
            abort(403, 'Unauthorized action.');
        }
        Session::put('page', 'report_management');

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        $salesExecutive = SalesExecutive::with(['user.country', 'user.state', 'user.district', 'user.block'])->findOrFail($id);

        // Institutions added by this sales executive
        $institutions = InstitutionManagement::with(['country', 'state', 'district', 'block'])
            ->where('added_by', $salesExecutive->user_id)
            ->get();

        // Students added by this sales executive (role_id 5)
        $students = User::with(['institution', 'country', 'state', 'district', 'block'])
            ->where('added_by', $salesExecutive->user_id)
            ->where('role_id', 5)
            ->get();

        // Report stats (same logic as sales-side report, but for this specific executive)
        $incomePerTarget = $salesExecutive->income_per_target ?? 0;

        $approvedStudents = User::where('added_by', $salesExecutive->user_id)
            ->where('role_id', 5)
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
            ->where('added_by', $salesExecutive->user_id)
            ->where('role_id', 5)
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
