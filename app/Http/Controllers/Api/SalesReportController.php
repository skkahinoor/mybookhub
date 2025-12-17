<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\SalesExecutive;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function getSalesReport(Request $request)
    {
        $sales = auth()->user();

        if (!$sales instanceof SalesExecutive) {
            return response()->json([
                'status' => false,
                'message' => 'Only Sales Executives allowed'
            ], 403);
        }

        $salesId = $sales->id;
        $incomePerTarget = $sales->income_per_target ?? 0;

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $todayStudents     = Student::where('added_by', $salesId)->where('status', 1)->whereDate('created_at', $today)->count();
        $weeklyStudents    = Student::where('added_by', $salesId)->where('status', 1)->whereDate('created_at', '>=', $startOfWeek)->count();
        $monthlyStudents   = Student::where('added_by', $salesId)->where('status', 1)->whereYear('created_at', $currentYear)->whereMonth('created_at', $currentMonth)->count();
        $totalStudents     = Student::where('added_by', $salesId)->where('status', 1)->count();

        $todayEarning   = $incomePerTarget * $todayStudents;
        $weeklyEarning  = $incomePerTarget * $weeklyStudents;
        $monthlyEarning = $incomePerTarget * $monthlyStudents;
        $totalEarning   = $incomePerTarget * $totalStudents;

        $days = 30;
        $startDate = now()->subDays($days - 1)->startOfDay();

        // Graph Dates
        $dates = [];
        $keys = [];
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($days - 1 - $i);
            $dates[] = $date->format('d M');
            $keys[] = $date->format('Y-m-d');
        }

        // Students per Day
        $studentGraph = Student::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('added_by', $salesId)
            ->where('status', 1)
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $dailyStudents = [];
        $dailyEarnings = [];
        foreach ($keys as $d) {
            $count = $studentGraph[$d] ?? 0;
            $dailyStudents[] = $count;
            $dailyEarnings[] = $count * $incomePerTarget;
        }

        return response()->json([
            'status' => true,
            'message' => "Report fetched successfully",
            'data' => [
                'summary' => [
                    'students_today'       => $todayStudents,
                    'students_week'        => $weeklyStudents,
                    'students_month'       => $monthlyStudents,
                    'students_total'       => $totalStudents,

                    'earning_today'        => $todayEarning,
                    'earning_week'         => $weeklyEarning,
                    'earning_month'        => $monthlyEarning,
                    'earning_total'        => $totalEarning,
                ],

                'graph' => [
                    'dates'            => $dates,
                    'students'         => $dailyStudents,
                    'earnings'         => $dailyEarnings,
                ]
            ]
        ], 200);
    }
}
