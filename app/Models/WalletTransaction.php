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
        $order = Order::with('orders_products.product.bookType')->find($orderId);
        if (! $order) {
            return;
        }

        // Allow crediting if the order is 'Paid' (Online) or 'Delivered' (COD)
        // OR if at least one item in the order has been 'Delivered'
        $isAnyItemDelivered = $order->orders_products->contains('item_status', 'Delivered');

        if (!in_array($order->order_status, ['Paid', 'Delivered']) && !$isAnyItemDelivered) {
            return;
        }

        $user = User::find($order->user_id);
        if (! $user || $user->is_wallet_credited == 1) {
            return;
        }

        // Check if any product in the order belongs to the 'testpaper' category (case-insensitive)
        // Check if any product in the order belongs to the 'testpaper' category (case-insensitive)
        $hasTestPaper = false;
        foreach ($order->orders_products as $orderProduct) {
            $categoryName = $orderProduct->product->bookType->book_type ?? '';

            // Normalize: remove spaces and lowercase
            $normalized = strtolower(str_replace(' ', '', $categoryName));

            if (in_array($normalized, [
                'testpaper',
                'testpapers',
                'testpaperbook',
                'testpaperbooks',
                'testbook',
                'testbooks',
            ], true)) {
                $hasTestPaper = true;
                break;
            }
        }

        if ($hasTestPaper) {
            // Credit 100 Rs
            $user->wallet_balance     += 100;
            $user->is_wallet_credited  = 1;
            $user->save();

            self::create([
                'user_id'     => $user->id,
                'order_id'    => $order->id,
                'amount'      => 100,
                'type'        => 'credit',
                'description' => 'Testpaper purchase bonus',
            ]);

            \App\Models\Notification::create([
                'type' => 'wallet_credit',
                'title' => 'Wallet credited',
                'message' => '₹100 has been added to your wallet as Testpaper purchase bonus (Order #' . $order->id . ').',
                'related_id' => (int) $user->id,
                'related_type' => User::class,
                'is_read' => false,
            ]);
        }
    }
    public static function revertWallet($orderId)
    {
        $order = Order::find($orderId);
        if (! $order) {
            return;
        }

        $user = User::find($order->user_id);
        if (! $user) {
            return;
        }

        // 1. Revert Bonus (Credit Reversal)
        $creditTransaction = self::where('order_id', $orderId)
            ->where('type', 'credit')
            ->where('description', 'LIKE', '%bonus%')
            ->first();
        if ($creditTransaction) {
            $user->wallet_balance     -= $creditTransaction->amount;
            $user->is_wallet_credited  = 0; // Allow them to get bonus again on next valid order
            $user->save();

            self::create([
                'user_id'     => $user->id,
                'order_id'    => $order->id,
                'amount'      => $creditTransaction->amount,
                'type'        => 'debit',
                'description' => 'Reversal of bonus for cancelled order #' . $order->id,
            ]);

            \App\Models\Notification::create([
                'type' => 'wallet_debit',
                'title' => 'Wallet adjusted',
                'message' => 'Bonus amount ₹' . $creditTransaction->amount . ' has been reversed for cancelled order #' . $order->id . '.',
                'related_id' => (int) $user->id,
                'related_type' => User::class,
                'is_read' => false,
            ]);
        }

        // 2. Revert Used Amount (Debit Reversal)
        $debitTransaction = self::where('order_id', $orderId)->where('type', 'debit')->where('description', 'NOT LIKE', '%Reversal%')->first();
        if ($debitTransaction) {
            $user->wallet_balance += $debitTransaction->amount;
            $user->save();

            self::create([
                'user_id'     => $user->id,
                'order_id'    => $order->id,
                'amount'      => $debitTransaction->amount,
                'type'        => 'credit',
                'description' => 'Refund of wallet amount for cancelled order #' . $order->id,
            ]);

            \App\Models\Notification::create([
                'type' => 'wallet_credit',
                'title' => 'Wallet refund',
                'message' => '₹' . $debitTransaction->amount . ' has been refunded to your wallet for cancelled order #' . $order->id . '.',
                'related_id' => (int) $user->id,
                'related_type' => User::class,
                'is_read' => false,
            ]);
        }
    }
}
