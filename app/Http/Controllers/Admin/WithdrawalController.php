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
    public function index(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'withdrawals');

        if ($request->ajax()) {
            $query = Withdrawal::with('salesExecutive')->select('withdrawals.*');

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('sales_executive', function ($row) {
                    $name = $row->salesExecutive->name ?? 'N/A';
                    $email = $row->salesExecutive->email ?? 'N/A';
                    return "<strong>{$name}</strong><br><small class='text-muted'>{$email}</small>";
                })
                ->editColumn('amount', function ($row) {
                    return "<strong>₹" . number_format($row->amount, 2) . "</strong>";
                })
                ->editColumn('payment_method', function ($row) {
                    if ($row->payment_method == 'bank_transfer') {
                        return '<span class="badge badge-info">Bank Transfer</span>';
                    } else {
                        return '<span class="badge badge-success">UPI</span>';
                    }
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'pending') {
                        return '<span class="badge badge-warning">Pending</span>';
                    } elseif ($row->status == 'approved') {
                        return '<span class="badge badge-info">Approved</span>';
                    } elseif ($row->status == 'completed') {
                        return '<span class="badge badge-success">Completed</span>';
                    } else {
                        return '<span class="badge badge-danger">Rejected</span>';
                    }
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y, h:i A');
                })
                ->addColumn('processed_date', function ($row) {
                    return $row->processed_at ? $row->processed_at->format('d M Y, h:i A') : 'N/A';
                })
                ->addColumn('actions', function ($row) {
                    $url = route('admin.withdrawals.show', $row->id);
                    return '<a href="' . $url . '" class="btn btn-sm btn-primary" title="View Details"><i class="mdi mdi-eye"></i></a>';
                })
                ->rawColumns(['sales_executive', 'amount', 'payment_method', 'status', 'actions'])
                ->make(true);
        }

        $minimumWithdrawal = (float) Setting::getValue('min_withdrawal_amount', 50);

        // Calculate statistics
        $pendingCount = Withdrawal::where('status', 'pending')->count();
        $approvedCount = Withdrawal::where('status', 'approved')->count();
        $completedCount = Withdrawal::where('status', 'completed')->count();
        $rejectedCount = Withdrawal::where('status', 'rejected')->count();
        $totalAmount = Withdrawal::whereIn('status', ['approved', 'completed'])->sum('amount');
        $pendingAmount = Withdrawal::where('status', 'pending')->sum('amount');

        return view('admin.withdrawals.index', compact(
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
            $withdrawal->processed_at = \Carbon\Carbon::now();
            if ($request->filled('transaction_id')) {
                $withdrawal->transaction_id = $request->transaction_id;
            }
        }

        $withdrawal->save();

        // If rejected, refund the wallet
        if ($request->status == 'rejected' && $oldStatus != 'rejected') {
            $user = $withdrawal->salesExecutive->user;
            if ($user) {
                $user->wallet_balance += $withdrawal->amount;
                $user->save();

                \App\Models\WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $withdrawal->amount,
                    'type' => 'credit',
                    'description' => 'Refund for Rejected Withdrawal (#' . $withdrawal->id . ')',
                ]);
            }
        }

        $statusMessages = [
            'approved' => 'Withdrawal request approved successfully.',
            'rejected' => 'Withdrawal request rejected.',
            'completed' => 'Withdrawal marked as completed.',
            'pending' => 'Withdrawal status reset to pending.',
        ];

        return redirect()->route('admin.withdrawals.show', $id)
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
