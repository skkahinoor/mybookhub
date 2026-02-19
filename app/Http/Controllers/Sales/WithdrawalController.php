<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Withdrawal;
use App\Models\User;
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

        $user = Auth::guard('sales')->user();
        $salesExecutive = $user->salesExecutive;
        $salesExecId = $salesExecutive->id ?? 0;
        $userId = $user->id;

        $withdrawals = Withdrawal::where('sales_executive_id', $salesExecId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate total earning from student enrollments + other wallet credits
        $incomePerTarget = \App\Models\Setting::getValue('default_income_per_target', 10);
        $totalStudents = User::where('added_by', $userId)->where('status', 1)->count();
        $totalEarning = $totalStudents * $incomePerTarget;

        // Add other wallet credits (excluding the ones for students to avoid double counting)
        $totalEarning += \App\Models\WalletTransaction::where('user_id', $userId)
            ->where('type', 'credit')
            ->where('description', 'NOT LIKE', 'Commission for Student%')
            ->where('description', 'NOT LIKE', 'Refund%')
            ->sum('amount');

        $minimumWithdrawal = (float) Setting::getValue('min_withdrawal_amount', 50);

        // Calculate total withdrawn and pending
        $totalWithdrawn = Withdrawal::where('sales_executive_id', $salesExecId)
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');

        $totalPending = Withdrawal::where('sales_executive_id', $salesExecId)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = $totalEarning - $totalWithdrawn - $totalPending;

        // Sync wallet_balance for consistency (optional but helps dashboard)
        if ($user->wallet_balance != $availableBalance) {
            $user->wallet_balance = $availableBalance;
            $user->save();
        }

        // Check if available balance meets the minimum withdrawal threshold
        $canWithdraw = $availableBalance >= $minimumWithdrawal;

        return view('sales.withdrawals.index', compact(
            'withdrawals',
            'availableBalance',
            'totalEarning',
            'totalWithdrawn',
            'canWithdraw',
            'minimumWithdrawal',
            'salesExecutive',
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

        $user = Auth::guard('sales')->user();
        $salesExecutive = $user->salesExecutive;
        $salesExecId = $salesExecutive->id ?? 0;
        $userId = $user->id;

        // Calculate total earning from student enrollments + other wallet credits
        $incomePerTarget = \App\Models\Setting::getValue('default_income_per_target', 10);
        $totalStudents = User::where('added_by', $userId)->where('status', 1)->count();
        $totalEarning = $totalStudents * $incomePerTarget;

        $totalEarning += \App\Models\WalletTransaction::where('user_id', $userId)
            ->where('type', 'credit')
            ->where('description', 'NOT LIKE', 'Commission for Student%')
            ->where('description', 'NOT LIKE', 'Refund%')
            ->sum('amount');

        $minimumWithdrawal = (float) Setting::getValue('min_withdrawal_amount', 50);

        $totalWithdrawn = Withdrawal::where('sales_executive_id', $salesExecId)
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');

        $totalPending = Withdrawal::where('sales_executive_id', $salesExecId)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = $totalEarning - $totalWithdrawn - $totalPending;

        // Sync wallet_balance for consistency
        if ($user->wallet_balance != $availableBalance) {
            $user->wallet_balance = $availableBalance;
            $user->save();
        }

        // Check if available balance meets the minimum withdrawal threshold
        if ($availableBalance < $minimumWithdrawal) {
            return redirect()->route('sales.withdrawals.index')
                ->with('error_message', "You must have at least ₹{$minimumWithdrawal} available before requesting a withdrawal.");
        }

        // Check if there's a pending withdrawal
        $pendingWithdrawal = Withdrawal::where('sales_executive_id', $salesExecId)
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
        $user = Auth::guard('sales')->user();
        $salesExecutive = $user->salesExecutive;
        $salesExecId = $salesExecutive->id ?? 0;
        $userId = $user->id;

        // Calculate total earning from student enrollments + other wallet credits
        $incomePerTarget = \App\Models\Setting::getValue('default_income_per_target', 10);
        $totalStudents = User::where('added_by', $userId)->where('status', 1)->count();
        $totalEarning = $totalStudents * $incomePerTarget;

        $totalEarning += \App\Models\WalletTransaction::where('user_id', $userId)
            ->where('type', 'credit')
            ->where('description', 'NOT LIKE', 'Commission for Student%')
            ->where('description', 'NOT LIKE', 'Refund%')
            ->sum('amount');

        $minimumWithdrawal = (float) Setting::getValue('min_withdrawal_amount', 50);

        $totalWithdrawn = Withdrawal::where('sales_executive_id', $salesExecId)
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');

        $totalPending = Withdrawal::where('sales_executive_id', $salesExecId)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = $totalEarning - $totalWithdrawn - $totalPending;

        // Sync wallet_balance for consistency
        if ($user->wallet_balance != $availableBalance) {
            $user->wallet_balance = $availableBalance;
            $user->save();
        }

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
        $pendingWithdrawal = Withdrawal::where('sales_executive_id', $salesExecId)
            ->where('status', 'pending')
            ->first();

        if ($pendingWithdrawal) {
            return redirect()->route('sales.withdrawals.index')
                ->with('error_message', 'You already have a pending withdrawal request.');
        }

        // Create withdrawal request
        $withdrawal = Withdrawal::create([
            'sales_executive_id' => $salesExecId,
            'amount' => $request->amount,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'remarks' => $request->remarks,
        ]);

        // Sync User wallet_balance (the new pending withdrawal will be subtracted on next load)
        // Since we just added a pending one, we should update current available balance
        $user->wallet_balance -= $request->amount;
        $user->save();

        // Create wallet transaction record
        \App\Models\WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'type' => 'debit',
            'description' => 'Withdrawal Request (#' . $withdrawal->id . ')',
        ]);

        // Create notification for admin
        Notification::create([
            'type' => 'withdrawal_request',
            'title' => 'New Withdrawal Request',
            'message' => "Sales executive '{$user->name}' has requested a withdrawal of ₹{$request->amount} via {$request->payment_method}.",
            'related_id' => $withdrawal->id,
            'related_type' => 'App\Models\Withdrawal',
            'is_read' => false,
        ]);

        return redirect()->route('sales.withdrawals.index')
            ->with('success_message', 'Withdrawal request submitted successfully. It will be processed shortly.');
    }
}
