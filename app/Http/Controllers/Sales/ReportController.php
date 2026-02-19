<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Student;
use App\Models\SalesExecutive;
use App\Models\User;
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

        // Get income_per_target from global settings
        $incomePerTarget = \App\Models\Setting::getValue('default_income_per_target', 10);

        // Only count approved students (status = 1, role_id = 5) for stats/earnings
        $approvedStudents = User::where('added_by', $salesExecutiveId)
            ->where('role_id', 5)
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

        // Calculate earnings from student enrollments (Count * Rate)
        $todayEarning = $todayStudentsCount * $incomePerTarget;
        $weeklyEarning = $weeklyStudentsCount * $incomePerTarget;
        $monthlyEarning = $monthlyStudentsCount * $incomePerTarget;
        $totalEarning = $totalStudentsCount * $incomePerTarget;

        // Add other wallet credits (e.g., Vendor Commissions)
        // We exclude 'Commission for Student' to avoid double counting if new logic is active
        $otherCreditsQuery = \App\Models\WalletTransaction::where('user_id', $salesExecutiveId)
            ->where('type', 'credit')
            ->where('description', 'NOT LIKE', 'Commission for Student%')
            ->where('description', 'NOT LIKE', 'Refund%');

        $todayEarning += (clone $otherCreditsQuery)->whereDate('created_at', Carbon::today())->sum('amount');
        $weeklyEarning += (clone $otherCreditsQuery)->whereDate('created_at', '>=', Carbon::now()->startOfWeek())->sum('amount');
        $monthlyEarning += (clone $otherCreditsQuery)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->sum('amount');
        $totalEarning += (clone $otherCreditsQuery)->sum('amount');

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

        $studentData = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('added_by', $salesExecutiveId)
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

        // Calculate earnings for graph from both sources
        $earningsData = [];
        foreach ($dateKeys as $dateKey) {
            $dailyStudentCount = User::where('added_by', $salesExecutiveId)
                ->where('role_id', 5)
                ->where('status', 1)
                ->whereDate('created_at', $dateKey)
                ->count();

            $dailyOtherCredits = \App\Models\WalletTransaction::where('user_id', $salesExecutiveId)
                ->where('type', 'credit')
                ->where('description', 'NOT LIKE', 'Commission for Student%')
                ->where('description', 'NOT LIKE', 'Refund%')
                ->whereDate('created_at', $dateKey)
                ->sum('amount');

            $earningsData[] = ($dailyStudentCount * $incomePerTarget) + $dailyOtherCredits;
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
