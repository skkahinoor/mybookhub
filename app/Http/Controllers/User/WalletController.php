<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        // Get all wallet transactions for the user, ordered by most recent
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate total credits and debits
        $totalCredits = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->sum('amount');

        $totalDebits = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->sum('amount');

        return view('user.wallet.index', compact(
            'logos',
            'headerLogo',
            'user',
            'transactions',
            'totalCredits',
            'totalDebits'
        ));
    }
}
