<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TransactionController extends Controller
{
    /**
     * Merged Earnings & Report page.
     * Shows today / weekly / monthly / total earnings stats + 30-day chart + transaction list.
     */
    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();
        Session::put('page', 'transactions');

        $salesExecutive   = Auth::guard('sales')->user();
        $salesExecutiveId = $salesExecutive->id;

        // ── Settings ──────────────────────────────────────────────────────
        $incomePerTarget = (float) Setting::getValue('default_income_per_target', 10);

        // ── Approved students (role 5, status 1) added by this exec ───────
        $approvedStudentsBase = User::where('added_by', $salesExecutiveId)
            ->where('role_id', 5)
            ->where('status', 1);

        $todayStudentsCount   = (clone $approvedStudentsBase)->whereDate('created_at', Carbon::today())->count();
        $weeklyStudentsCount  = (clone $approvedStudentsBase)->whereDate('created_at', '>=', Carbon::now()->startOfWeek())->count();
        $monthlyStudentsCount = (clone $approvedStudentsBase)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
        $totalStudentsCount   = (clone $approvedStudentsBase)->count();

        // ── Approved vendors added by this exec (Free vs Pro) ──────────
        //    Join with vendors table to get CURRENT plan
        $vendorQuery = User::where('users.added_by', $salesExecutiveId)
            ->where('users.role_id', 2)
            ->where('users.status', 1)
            ->join('vendors', 'vendors.user_id', '=', 'users.id');

        $freeVendorCount = (clone $vendorQuery)->where('vendors.plan', 'free')->count();
        $proVendorCount  = (clone $vendorQuery)->where('vendors.plan', 'pro')->count();

        // ── Student-based earnings ────────────────────────────────────────
        $todayEarning   = $todayStudentsCount   * $incomePerTarget;
        $weeklyEarning  = $weeklyStudentsCount  * $incomePerTarget;
        $monthlyEarning = $monthlyStudentsCount * $incomePerTarget;
        $totalEarning   = $totalStudentsCount   * $incomePerTarget;

        // ── Other wallet credits (vendor commissions, sale commissions …) ──
        //    Exclude "Commission for Student%" to avoid double-counting
        //    Exclude "Refund%" to keep earnings accurate
        $otherCreditsBase = WalletTransaction::where('user_id', $salesExecutiveId)
            ->where('type', 'credit')
            ->where('description', 'NOT LIKE', 'Commission for Student%')
            ->where('description', 'NOT LIKE', 'Refund%');

        $todayEarning   += (clone $otherCreditsBase)->whereDate('created_at', Carbon::today())->sum('amount');
        $weeklyEarning  += (clone $otherCreditsBase)->whereDate('created_at', '>=', Carbon::now()->startOfWeek())->sum('amount');
        $monthlyEarning += (clone $otherCreditsBase)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->sum('amount');
        $totalEarning   += (clone $otherCreditsBase)->sum('amount');

        // ── 30-day chart data ─────────────────────────────────────────────
        $days      = 30;
        $startDate = now()->subDays($days - 1)->startOfDay();
        $dates     = [];
        $dateKeys  = [];

        for ($i = 0; $i < $days; $i++) {
            $date      = now()->subDays($days - 1 - $i);
            $dates[]   = $date->format('d M');
            $dateKeys[] = $date->format('Y-m-d');
        }

        $studentByDate = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('added_by', $salesExecutiveId)
            ->where('role_id', 5)
            ->where('status', 1)
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $studentsCount = [];
        $earningsData  = [];

        foreach ($dateKeys as $dateKey) {
            $daily            = $studentByDate[$dateKey] ?? 0;
            $studentsCount[]  = $daily;
            $otherDaily       = (clone $otherCreditsBase)->whereDate('created_at', $dateKey)->sum('amount');
            $earningsData[]   = ($daily * $incomePerTarget) + $otherDaily;
        }

        // Paginated transaction list — credits only (withdrawals shown on their own page)
        $transactions = WalletTransaction::where('user_id', $salesExecutiveId)
            ->where('type', 'credit')
            ->where('description', 'NOT LIKE', 'Refund%')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('sales.transactions.index', compact(
            'todayEarning',
            'weeklyEarning',
            'monthlyEarning',
            'totalEarning',
            'todayStudentsCount',
            'weeklyStudentsCount',
            'monthlyStudentsCount',
            'totalStudentsCount',
            'freeVendorCount',
            'proVendorCount',
            'incomePerTarget',
            'dates',
            'studentsCount',
            'earningsData',
            'transactions',
            'logos',
            'headerLogo'
        ));
    }
}
