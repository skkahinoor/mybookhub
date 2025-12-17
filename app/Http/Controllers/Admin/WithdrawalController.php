<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\HeaderLogo;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of all withdrawal requests.
     */
    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'withdrawals');

        $minimumWithdrawal = (float) Setting::getValue('min_withdrawal_amount', 50);

        $withdrawals = Withdrawal::with('salesExecutive')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $pendingCount = Withdrawal::where('status', 'pending')->count();
        $approvedCount = Withdrawal::where('status', 'approved')->count();
        $completedCount = Withdrawal::where('status', 'completed')->count();
        $rejectedCount = Withdrawal::where('status', 'rejected')->count();
        $totalAmount = Withdrawal::whereIn('status', ['approved', 'completed'])->sum('amount');
        $pendingAmount = Withdrawal::where('status', 'pending')->sum('amount');

        return view('admin.withdrawals.index', compact(
            'withdrawals',
            'pendingCount',
            'approvedCount',
            'completedCount',
            'rejectedCount',
            'totalAmount',
            'pendingAmount',
            'minimumWithdrawal',
            'logos',
            'headerLogo'
        ));
    }

    /**
     * Show the specified withdrawal request details.
     */
    public function show($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'withdrawals');

        $withdrawal = Withdrawal::with('salesExecutive')->findOrFail($id);

        return view('admin.withdrawals.show', compact('withdrawal', 'logos', 'headerLogo'));
    }

    /**
     * Update withdrawal status (approve, reject, complete).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
            'remarks' => 'nullable|string|max:500',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $withdrawal = Withdrawal::findOrFail($id);
        $oldStatus = $withdrawal->status;

        $withdrawal->status = $request->status;
        $withdrawal->remarks = $request->remarks;

        if ($request->status == 'completed' || $request->status == 'approved') {
            $withdrawal->processed_at = Carbon::now();
            if ($request->filled('transaction_id')) {
                $withdrawal->transaction_id = $request->transaction_id;
            }
        }

        $withdrawal->save();

        $statusMessages = [
            'approved' => 'Withdrawal request approved successfully.',
            'rejected' => 'Withdrawal request rejected.',
            'completed' => 'Withdrawal marked as completed.',
            'pending' => 'Withdrawal status reset to pending.',
        ];

        return redirect()->route('admin.withdrawals.index')
            ->with('success_message', $statusMessages[$request->status] ?? 'Status updated successfully.');
    }

    /**
     * Update minimum withdrawal amount managed by admin.
     */
    public function updateMinimum(Request $request)
    {
        $request->validate([
            'minimum_withdrawal_amount' => 'required|numeric|min:1',
        ]);

        Setting::setValue('min_withdrawal_amount', $request->minimum_withdrawal_amount);

        return redirect()->route('admin.withdrawals.index')
            ->with('success_message', 'Minimum withdrawal amount updated successfully.');
    }
}
