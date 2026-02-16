<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InstitutionManagement;
use App\Models\User;
use App\Models\SalesExecutive;
use Carbon\Carbon;

class SalesDashboardController extends Controller
{
    private function detectUserType($user)
    {
        if (!$user) {
            return null;
        }

        if ($user->hasRole('sales')) {
            return 'sales';
        }

        return null;
    }

    public function todayInstitutes(Request $request)
    {
        $user = $request->user();

        if ($this->detectUserType($user) !== 'sales') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Sales can access this.'
            ], 403);
        }

        if ($user->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        $today = now()->toDateString();

        $query = InstitutionManagement::where('added_by', $user->id)
            ->where('status', 1)
            ->whereDate('created_at', $today);

        return response()->json([
            'status' => true,
            'message' => 'Today added institutes fetched successfully.',
            'total' => $query->count(),
            'data' => $query->get()
        ]);
    }

    public function totalInstitutes(Request $request)
    {
        $user = $request->user();

        if ($this->detectUserType($user) !== 'sales') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Sales can access this.'
            ], 403);
        }

        if ($user->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        $query = InstitutionManagement::where('added_by', $user->id)
            ->where('status', 1);

        return response()->json([
            'status' => true,
            'message' => 'Total institutes fetched successfully.',
            'total' => $query->count(),
            'data' => $query->get()
        ]);
    }

    public function todayStudents(Request $request)
    {
        $user = $request->user();

        if ($this->detectUserType($user) !== 'sales') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Sales can access this.'
            ], 403);
        }

        if ($user->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        $today = now()->toDateString();

        $query = User::where('added_by', $user->id)
            ->where('status', 1)
            ->whereDate('created_at', $today);

        return response()->json([
            'status' => true,
            'message' => 'Today added students fetched successfully.',
            'total' => $query->count(),
            'data' => $query->get()
        ]);
    }

    public function totalStudents(Request $request)
    {
        $user = $request->user();

        if ($this->detectUserType($user) !== 'sales') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Sales can access this.'
            ], 403);
        }

        if ($user->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        $query = User::where('added_by', $user->id)
            ->where('status', 1);

        return response()->json([
            'status' => true,
            'message' => 'Total students fetched successfully.',
            'total' => $query->count(),
            'data' => $query->get()
        ]);
    }

    public function graphDashboard(Request $request)
    {
        $user = $request->user();

        if ($this->detectUserType($user) !== 'sales') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Sales can access this.'
            ], 403);
        }

        if ($user->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        $days = 10;
        $startDate = now()->subDays($days - 1)->toDateString();

        $dates = collect(range(0, $days - 1))
            ->map(fn($i) => now()->subDays($days - 1 - $i)->toDateString())
            ->toArray();

        $instituteData = InstitutionManagement::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('added_by', $user->id)
            ->where('status', 1)
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $studentData = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('added_by', $user->id)
            ->where('status', 1)
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $institutes = [];
        $students = [];

        foreach ($dates as $date) {
            $institutes[] = $instituteData[$date] ?? 0;
            $students[] = $studentData[$date] ?? 0;
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
