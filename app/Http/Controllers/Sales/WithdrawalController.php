<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Withdrawal;
use App\Models\Student;
use App\Models\SalesExecutive;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of withdrawal requests.
     */
    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'withdrawals');

        $salesExecutive = Auth::guard('sales')->user();
        $salesExecutiveId = $salesExecutive->id;

        $withdrawals = Withdrawal::where('sales_executive_id', $salesExecutiveId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate available balance
        $totalStudents = Student::where('added_by', $salesExecutiveId)->where('status', 1)->count();
        $incomePerTarget = $salesExecutive->income_per_target ?? 0;
        $totalEarning = $incomePerTarget * $totalStudents;

        $minimumWithdrawal = (float) Setting::getValue('min_withdrawal_amount', 50);

        // Calculate total withdrawn amount
        $totalWithdrawn = Withdrawal::where('sales_executive_id', $salesExecutiveId)
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');

        $availableBalance = $totalEarning - $totalWithdrawn;

        // Check if available balance meets the minimum withdrawal threshold
        $canWithdraw = $availableBalance >= $minimumWithdrawal;

        return view('sales.withdrawals.index', compact(
            'withdrawals',
            'availableBalance',
            'totalEarning',
            'totalWithdrawn',
            'canWithdraw',
            'minimumWithdrawal',
            'logos',
            'headerLogo'
        ));
    }

    /**
     * Show the form for creating a new withdrawal request.
     */
    public function create()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'withdrawals');

        $salesExecutive = Auth::guard('sales')->user();
        $salesExecutiveId = $salesExecutive->id;

        // Calculate available balance
        $totalStudents = Student::where('added_by', $salesExecutiveId)->where('status', 1)->count();
        $incomePerTarget = $salesExecutive->income_per_target ?? 0;
        $totalEarning = $incomePerTarget * $totalStudents;

        $minimumWithdrawal = (float) Setting::getValue('min_withdrawal_amount', 50);

        $totalWithdrawn = Withdrawal::where('sales_executive_id', $salesExecutiveId)
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');

        $availableBalance = $totalEarning - $totalWithdrawn;

        // Check if available balance meets the minimum withdrawal threshold
        if ($availableBalance < $minimumWithdrawal) {
            return redirect()->route('sales.withdrawals.index')
                ->with('error_message', "You must have at least ₹{$minimumWithdrawal} available before requesting a withdrawal.");
        }

        // Check if there's a pending withdrawal
        $pendingWithdrawal = Withdrawal::where('sales_executive_id', $salesExecutiveId)
            ->where('status', 'pending')
            ->first();

        if ($pendingWithdrawal) {
            return redirect()->route('sales.withdrawals.index')
                ->with('error_message', 'You already have a pending withdrawal request. Please wait for it to be processed.');
        }

        return view('sales.withdrawals.create', compact(
            'availableBalance',
            'salesExecutive',
            'minimumWithdrawal',
            'logos',
            'headerLogo'
        ));
    }

    /**
     * Store a newly created withdrawal request.
     */
    public function store(Request $request)
    {
        $salesExecutive = Auth::guard('sales')->user();
        $salesExecutiveId = $salesExecutive->id;

        // Calculate available balance
        $totalStudents = Student::where('added_by', $salesExecutiveId)->where('status', 1)->count();
        $incomePerTarget = $salesExecutive->income_per_target ?? 0;
        $totalEarning = $incomePerTarget * $totalStudents;

        $minimumWithdrawal = (float) Setting::getValue('min_withdrawal_amount', 50);

        $totalWithdrawn = Withdrawal::where('sales_executive_id', $salesExecutiveId)
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');

        $availableBalance = $totalEarning - $totalWithdrawn;

        // Check if available balance meets the minimum withdrawal threshold
        if ($availableBalance < $minimumWithdrawal) {
            return redirect()->route('sales.withdrawals.index')
                ->with('error_message', "You must have at least ₹{$minimumWithdrawal} available before requesting a withdrawal.");
        }

        // Validate request
        $request->validate([
            // Once threshold is reached, allow withdrawing any positive amount up to available balance
            'amount' => 'required|numeric|min:1|max:' . $availableBalance,
            'payment_method' => 'required|string|in:bank_transfer,upi',
            'remarks' => 'nullable|string|max:500',
        ]);

        // Check if there's a pending withdrawal
        $pendingWithdrawal = Withdrawal::where('sales_executive_id', $salesExecutiveId)
            ->where('status', 'pending')
            ->first();

        if ($pendingWithdrawal) {
            return redirect()->route('sales.withdrawals.index')
                ->with('error_message', 'You already have a pending withdrawal request.');
        }

        // Create withdrawal request
        $withdrawal = Withdrawal::create([
            'sales_executive_id' => $salesExecutiveId,
            'amount' => $request->amount,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'remarks' => $request->remarks,
        ]);

        // Create notification for admin
        Notification::create([
            'type' => 'withdrawal_request',
            'title' => 'New Withdrawal Request',
            'message' => "Sales executive '{$salesExecutive->name}' has requested a withdrawal of ₹{$request->amount} via {$request->payment_method}.",
            'related_id' => $withdrawal->id,
            'related_type' => 'App\Models\Withdrawal',
            'is_read' => false,
        ]);

        return redirect()->route('sales.withdrawals.index')
            ->with('success_message', 'Withdrawal request submitted successfully. It will be processed shortly.');
    }
}
