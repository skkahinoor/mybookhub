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
    private function checkAccess(Request $request, array $allowedRoles = ['sales'])
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // 🔐 Auth check
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // 🔎 Fetch role from roles table
        $role = \Spatie\Permission\Models\Role::find($user->role_id);

        if (!$role || !in_array($role->name, $allowedRoles)) {
            return response()->json([
                'status'  => false,
                'message' => 'Only Admin or Vendor can access this.'
            ], 403);
        }

        // 🔒 Status check
        if ($user->status != 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }
        return null;
    }

    public function todayInstitutes(Request $request)
    {
        if ($resp = $this->checkAccess($request, ['sales'])) {
            return $resp;
        }

        $user = $request->user();
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
        if ($resp = $this->checkAccess($request, ['sales'])) {
            return $resp;
        }

        $user = $request->user();

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
        if ($resp = $this->checkAccess($request, ['sales'])) {
            return $resp;
        }

        $user = $request->user();
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
        if ($resp = $this->checkAccess($request, ['sales'])) {
            return $resp;
        }

        $user = $request->user();

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
        if ($resp = $this->checkAccess($request, ['sales'])) {
            return $resp;
        }

        $user = $request->user();

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
