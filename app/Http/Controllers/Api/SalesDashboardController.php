<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InstitutionManagement;
use App\Models\Student;
use App\Models\SalesExecutive;
use Carbon\Carbon;

class SalesDashboardController extends Controller
{

    public function todayInstitutes(Request $request)
    {
        $sales = $request->user();

        if (!$sales instanceof SalesExecutive) {
            return response()->json([
                'status' => false,
                'message' => 'Only Sales Executives can access this.'
            ], 403);
        }

        $today = Carbon::today();

        $institutes = InstitutionManagement::where('added_by', $sales->id)
            ->where('status', 1)
            ->whereDate('created_at', $today)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Today added institutes fetched successfully.',
            'total' => $institutes->count(),
            'data' => $institutes
        ]);
    }

    public function totalInstitutes(Request $request)
    {
        $sales = $request->user();

        if (!$sales instanceof SalesExecutive) {
            return response()->json([
                'status' => false,
                'message' => 'Only Sales Executives can access this.'
            ], 403);
        }

        $institutes = InstitutionManagement::where('added_by', $sales->id)->where('status', 1)->get();

        return response()->json([
            'status' => true,
            'message' => 'Total institutes fetched successfully.',
            'total' => $institutes->count(),
            'data' => $institutes
        ]);
    }


    public function todayStudents(Request $request)
    {
        $sales = $request->user();

        if (!$sales instanceof SalesExecutive) {
            return response()->json([
                'status' => false,
                'message' => 'Only Sales Executives can access this.'
            ], 403);
        }

        $today = Carbon::today();

        $students = Student::where('added_by', $sales->id)
            ->where('status', 1)
            ->whereDate('created_at', $today)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Today added students fetched successfully.',
            'total' => $students->count(),
            'data' => $students
        ]);
    }

    public function totalStudents(Request $request)
    {
        $sales = $request->user();

        if (!$sales instanceof SalesExecutive) {
            return response()->json([
                'status' => false,
                'message' => 'Only Sales Executives can access this.'
            ], 403);
        }

        $students = Student::where('added_by', $sales->id)->where('status', 1)->get();

        return response()->json([
            'status' => true,
            'message' => 'Total students fetched successfully.',
            'total' => $students->count(),
            'data' => $students
        ]);
    }
    public function graphDashboard(Request $request)
    {
        $sales = auth()->user();

        if (!$sales instanceof SalesExecutive) {
            return response()->json([
                'status' => false,
                'message' => 'Only Sales Executives allowed'
            ], 403);
        }

        $days = 10;
        $startDate = now()->subDays($days - 1)->toDateString();

        $dates = [];
        for ($i = 0; $i < $days; $i++) {
            $dates[] = now()->subDays($days - 1 - $i)->toDateString();
        }

        $instituteData = InstitutionManagement::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('added_by', $sales->id)
            ->where('status', 1)
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $studentData = Student::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('added_by', $sales->id)
            ->where('status', 1)
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $institutes = [];
        $students = [];

        foreach ($dates as $date) {
            $institutes[] = $instituteData[$date] ?? 0;
            $students[]   = $studentData[$date] ?? 0;
        }

        return response()->json([
            'status' => true,
            'message' => 'Graph data fetched successfully',
            'data' => [
                'dates'      => $dates,
                'institutes' => $institutes,
                'students'   => $students
            ]
        ]);
    }
}
