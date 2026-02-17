<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'type',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public static function checkAndCreditWallet($orderId)
    {
        $order = Order::with('orders_products.product.category')->find($orderId);
        if (!$order) return;

        // Ensure we don't credit for Pending orders (Razorpay pending flow)
        if ($order->order_status == 'Pending') return;

        $user = User::find($order->user_id);
        if (!$user || $user->is_wallet_credited == 1) return;

        // Check if any product in the order belongs to the 'testpaper' category (case-insensitive)
        $hasTestPaper = false;
        foreach ($order->orders_products as $orderProduct) {
            $categoryName = $orderProduct->product->category->category_name ?? '';
            if (strtolower($categoryName) == 'testpaper') {
                $hasTestPaper = true;
                break;
            }
        }

        if ($hasTestPaper) {
            // Credit 100 Rs
            $user->wallet_balance += 100;
            $user->is_wallet_credited = 1;
            $user->save();

            self::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => 100,
                'type' => 'credit',
                'description' => 'Testpaper purchase bonus',
            ]);
        }
    }
    public static function revertWallet($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) return;

        $user = User::find($order->user_id);
        if (!$user) return;

        // 1. Revert Bonus (Credit Reversal)
        $creditTransaction = self::where('order_id', $orderId)
            ->where('type', 'credit')
            ->where('description', 'LIKE', '%bonus%')
            ->first();
        if ($creditTransaction) {
            $user->wallet_balance -= $creditTransaction->amount;
            $user->is_wallet_credited = 0; // Allow them to get bonus again on next valid order
            $user->save();

            self::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $creditTransaction->amount,
                'type' => 'debit',
                'description' => 'Reversal of bonus for cancelled order #' . $order->id,
            ]);
        }

        // 2. Revert Used Amount (Debit Reversal)
        $debitTransaction = self::where('order_id', $orderId)->where('type', 'debit')->where('description', 'NOT LIKE', '%Reversal%')->first();
        if ($debitTransaction) {
            $user->wallet_balance += $debitTransaction->amount;
            $user->save();

            self::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $debitTransaction->amount,
                'type' => 'credit',
                'description' => 'Refund of wallet amount for cancelled order #' . $order->id,
            ]);
        }
    }
}
