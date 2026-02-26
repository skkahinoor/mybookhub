<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use App\Models\Withdrawal;
use App\Models\Setting;
use App\Models\WalletTransaction;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WithdrawalApiController extends Controller
{

    public function dashboard(Request $request)
    {
        $user = $request->user();

        if ($user->role_id != RoleHelper::salesId()) {
            return response()->json([
                'status' => false,
                'message' => 'Only Sales Executives can access this.'
            ], 403);
        }

        $salesExecutive = $user->salesExecutive;
        $salesExecId = $salesExecutive->id ?? 0;
        $userId = $user->id;

        $incomePerTarget = (float) Setting::getValue('default_income_per_target', 10);

        $totalStudents = User::where('added_by', $userId)
            ->where('role_id', RoleHelper::studentId())
            ->where('status', 1)
            ->count();

        $totalEarning = $totalStudents * $incomePerTarget;

        $totalEarning += WalletTransaction::where('user_id', $userId)
            ->where('type', 'credit')
            ->where('description', 'NOT LIKE', 'Commission for Student%')
            ->where('description', 'NOT LIKE', 'Refund%')
            ->sum('amount');


        $totalWithdrawn = Withdrawal::where('sales_executive_id', $salesExecId)
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');

        $totalPending = Withdrawal::where('sales_executive_id', $salesExecId)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = $totalEarning - $totalWithdrawn - $totalPending;

        if ($user->wallet_balance != $availableBalance) {
            $user->wallet_balance = $availableBalance;
            $user->save();
        }

        $minimumWithdrawal = (float) Setting::getValue('min_withdrawal_amount', 50);

        $withdrawals = Withdrawal::where('sales_executive_id', $salesExecId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Withdrawal dashboard loaded successfully',
            'data' => [
                'total_earning'     => $totalEarning,
                'total_withdrawn'   => $totalWithdrawn,
                'total_pending'     => $totalPending,
                'available_balance' => $availableBalance,
                'minimum_withdrawal' => $minimumWithdrawal,
                'can_withdraw'      => $availableBalance >= $minimumWithdrawal,
                'withdrawals'       => $withdrawals
            ]
        ]);
    }

    public function requestWithdraw(Request $request)
    {
        $user = $request->user();
        $salesExecutive = $user->salesExecutive;
        $salesExecId = $salesExecutive->id ?? 0;
        $userId = $user->id;

        $incomePerTarget = (float) Setting::getValue('default_income_per_target', 10);

        $totalStudents = User::where('added_by', $userId)
            ->where('role_id', RoleHelper::studentId())
            ->where('status', 1)
            ->count();

        $totalEarning = $totalStudents * $incomePerTarget;

        $totalEarning += WalletTransaction::where('user_id', $userId)
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

        if ($user->wallet_balance != $availableBalance) {
            $user->wallet_balance = $availableBalance;
            $user->save();
        }

        if ($availableBalance < $minimumWithdrawal) {
            return response()->json([
                'status' => false,
                'message' => "You must have at least ₹{$minimumWithdrawal} available before requesting withdrawal."
            ], 403);
        }

        $pendingWithdrawal = Withdrawal::where('sales_executive_id', $salesExecId)
            ->where('status', 'pending')
            ->first();

        if ($pendingWithdrawal) {
            return response()->json([
                'status' => false,
                'message' => 'You already have a pending withdrawal request.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1|max:' . $availableBalance,
            'payment_method' => 'required|string|in:bank_transfer,upi',
            'remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $withdrawal = Withdrawal::create([
            'sales_executive_id' => $salesExecId,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'remarks' => $validated['remarks'] ?? null,
            'status' => 'pending'
        ]);

        $user->wallet_balance -= $validated['amount'];
        $user->save();

        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'type' => 'debit',
            'description' => 'Withdrawal Request (#' . $withdrawal->id . ')',
        ]);

        Notification::create([
            'type' => 'withdrawal_request',
            'title' => 'New Withdrawal Request',
            'message' => "Sales executive '{$user->name}' requested ₹{$validated['amount']} via {$validated['payment_method']}.",
            'related_id' => $withdrawal->id,
            'related_type' => Withdrawal::class,
            'is_read' => false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Withdrawal request submitted successfully.'
        ], 201);
    }

    public function sendWithdrawRequestSMS($phone, $amount)
    {
        $to = '91' . preg_replace('/[^0-9]/', '', $phone);

        try {
            $client = new Client();

            $payload = [
                "template_id" => env('MSG91_WITHDRAW_REQUEST_TEMPLATE_ID'), // Your MSG91 Template ID
                "recipients" => [
                    [
                        "mobiles" => $to,
                        "amount" => $amount
                    ]
                ]
            ];

            Log::info("Withdrawal Request SMS Payload", $payload);

            $client->post("https://control.msg91.com/api/v5/flow/", [
                'json' => $payload,
                'headers' => [
                    'accept' => 'application/json',
                    'authkey' => env('MSG91_AUTH_KEY'),
                    'content-type' => 'application/json'
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Withdrawal Request SMS ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function transactions(Request $request)
    {
        $salesExecutive = $request->user(); // sanctum user
        $salesExecutiveId = $salesExecutive->id;
        $salesExecutiveProfile = $salesExecutive->salesExecutive;

        // ── Settings ─────────────────────────────────────
        $incomePerTarget = $salesExecutiveProfile
            ? $salesExecutiveProfile->income_per_target
            : 0;

        if (!$incomePerTarget) {
            $incomePerTarget = (float) Setting::getValue('default_income_per_target', 10);
        }

        // ── Approved Students ───────────────────────────
        $approvedStudentsBase = User::where('added_by', $salesExecutiveId)
            ->where('role_id', RoleHelper::studentId())
            ->where('status', 1);

        $todayStudentsCount   = (clone $approvedStudentsBase)->whereDate('created_at', Carbon::today())->count();
        $weeklyStudentsCount  = (clone $approvedStudentsBase)->whereDate('created_at', '>=', Carbon::now()->startOfWeek())->count();
        $monthlyStudentsCount = (clone $approvedStudentsBase)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $totalStudentsCount   = (clone $approvedStudentsBase)->count();

        // ── Vendors (Free / Pro) ────────────────────────
        $vendorQuery = User::where('users.added_by', $salesExecutiveId)
            ->where('users.role_id', RoleHelper::vendorId())
            ->where('users.status', 1)
            ->join('vendors', 'vendors.user_id', '=', 'users.id');

        $freeVendorCount = (clone $vendorQuery)->where('vendors.plan', 'free')->count();
        $proVendorCount  = (clone $vendorQuery)->where('vendors.plan', 'pro')->count();

        // ── Student Earnings ─────────────────────────────
        $todayEarning   = $todayStudentsCount   * $incomePerTarget;
        $weeklyEarning  = $weeklyStudentsCount  * $incomePerTarget;
        $monthlyEarning = $monthlyStudentsCount * $incomePerTarget;
        $totalEarning   = $totalStudentsCount   * $incomePerTarget;

        // ── Other Wallet Credits ─────────────────────────
        $otherCreditsBase = WalletTransaction::where('user_id', $salesExecutiveId)
            ->where('type', 'credit')
            ->where('description', 'NOT LIKE', 'Commission for Student%')
            ->where('description', 'NOT LIKE', 'Refund%');

        $todayEarning   += (clone $otherCreditsBase)->whereDate('created_at', Carbon::today())->sum('amount');
        $weeklyEarning  += (clone $otherCreditsBase)->whereDate('created_at', '>=', Carbon::now()->startOfWeek())->sum('amount');
        $monthlyEarning += (clone $otherCreditsBase)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('amount');
        $totalEarning   += (clone $otherCreditsBase)->sum('amount');

        // ── 30 Days Chart ────────────────────────────────
        $days = 30;
        $startDate = now()->subDays($days - 1)->startOfDay();
        $dates = [];
        $earningsData = [];
        $studentsCount = [];

        $studentByDate = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('added_by', $salesExecutiveId)
            ->where('role_id', RoleHelper::studentId())
            ->where('status', 1)
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        for ($i = 0; $i < $days; $i++) {

            $date = now()->subDays($days - 1 - $i);
            $dateKey = $date->format('Y-m-d');

            $dailyStudents = $studentByDate[$dateKey] ?? 0;

            $otherDaily = (clone $otherCreditsBase)
                ->whereDate('created_at', $dateKey)
                ->sum('amount');

            $studentsCount[] = $dailyStudents;
            $earningsData[] = ($dailyStudents * $incomePerTarget) + $otherDaily;
            $dates[] = $date->format('d M');
        }

        // ── Transactions (Paginated) ─────────────────────
        $transactions = WalletTransaction::where('user_id', $salesExecutiveId)
            ->where('type', 'credit')
            ->where('description', 'NOT LIKE', 'Refund%')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'status' => true,
            'message' => 'Transaction dashboard data fetched successfully',
            'data' => [
                'earnings' => [
                    'today'   => $todayEarning,
                    'weekly'  => $weeklyEarning,
                    'monthly' => $monthlyEarning,
                    'total'   => $totalEarning,
                ],
                'students' => [
                    'today'   => $todayStudentsCount,
                    'weekly'  => $weeklyStudentsCount,
                    'monthly' => $monthlyStudentsCount,
                    'total'   => $totalStudentsCount,
                ],
                'vendors' => [
                    'free' => $freeVendorCount,
                    'pro'  => $proVendorCount,
                ],
                'income_per_target' => $incomePerTarget,
                'chart' => [
                    'dates' => $dates,
                    'students_count' => $studentsCount,
                    'earnings' => $earningsData,
                ],
                'transactions' => $transactions
            ]
        ]);
    }
}
