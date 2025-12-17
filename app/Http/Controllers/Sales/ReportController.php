<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Student;
use App\Models\SalesExecutive;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{
    /**
     * Display comprehensive report with earnings and student graph for the current sales executive.
     */
    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'reports');

        $salesExecutive = Auth::guard('sales')->user();
        $salesExecutiveId = $salesExecutive->id;

        // Get income_per_target from sales executive
        $incomePerTarget = $salesExecutive->income_per_target ?? 0;

        // Only count approved students (status = 1) for stats/earnings
        $approvedStudents = Student::where('added_by', $salesExecutiveId)
            ->where('status', 1);

        // Calculate today's students
        $todayStudentsCount = (clone $approvedStudents)
            ->whereDate('created_at', Carbon::today())
            ->count();

        // Calculate weekly students (last 7 days)
        $weeklyStudentsCount = (clone $approvedStudents)
            ->whereDate('created_at', '>=', Carbon::now()->startOfWeek())
            ->count();

        // Calculate monthly students (current month)
        $monthlyStudentsCount = (clone $approvedStudents)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        // Calculate total students
        $totalStudentsCount = (clone $approvedStudents)->count();

        // Calculate earnings
        $todayEarning = $incomePerTarget * $todayStudentsCount;
        $weeklyEarning = $incomePerTarget * $weeklyStudentsCount;
        $monthlyEarning = $incomePerTarget * $monthlyStudentsCount;
        $totalEarning = $incomePerTarget * $totalStudentsCount;

        // Prepare graph data for last 30 days
        $days = 30;
        $startDate = now()->subDays($days - 1)->startOfDay();

        $dates = [];
        $dateKeys = [];
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($days - 1 - $i);
            $dates[] = $date->format('d M');
            $dateKeys[] = $date->format('Y-m-d');
        }

        $studentData = Student::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('added_by', $salesExecutiveId)
            ->where('status', 1)
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $studentsCount = [];
        foreach ($dateKeys as $dateKey) {
            $studentsCount[] = $studentData[$dateKey] ?? 0;
        }

        // Calculate earnings for graph (students * income_per_target)
        $earningsData = [];
        foreach ($studentsCount as $count) {
            $earningsData[] = $count * $incomePerTarget;
        }

        return view('sales.reports.index')->with(compact(
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

