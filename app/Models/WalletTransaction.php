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
        if (!$order) {
            return 0;
        }

        // Allow crediting if the order is 'Paid' (Online) or 'Delivered' (COD)
        // OR if at least one item in the order has been 'Delivered'
        $isAnyItemDelivered = $order->orders_products->contains('item_status', 'Delivered');

        if (!in_array($order->order_status, ['Paid', 'Delivered']) && !$isAnyItemDelivered) {
            return 0;
        }

        $user = User::find($order->user_id);
        if (!$user) { 
            return 0;
        }

        $totalCashback = 0;

        // --- TestPaper Bonus Logic (One-time) ---
        if ($user->is_wallet_credited != 1) {
            $hasTestPaper = false;
            foreach ($order->orders_products as $orderProduct) {
                $categoryName = $orderProduct->product->bookType->book_type ?? '';
                $normalized = strtolower(str_replace(' ', '', $categoryName));

                if (in_array($normalized, [
                    'testpaper', 'testpapers', 'testpaperbook', 'testpaperbooks', 'testbook', 'testbooks',
                ], true)) {
                    $hasTestPaper = true;
                    break;
                }
            }

            if ($hasTestPaper) {
                $user->wallet_balance += 100;
                $user->is_wallet_credited = 1;
                $user->save();

                $totalCashback += 100;

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

        // --- MOV Cashback Logic (Per Checkout Group) ---
        // Since the system creates a separate Order record for each item in the cart,
        // we need to sum up all related orders placed in this checkout session.
        
        $groupOrdersQuery = Order::where('user_id', $order->user_id)
            ->where('payment_gateway', $order->payment_gateway);

        if ($order->razorpay_order_id) {
            // Online: group by razorpay_order_id
            $groupOrdersQuery->where('razorpay_order_id', $order->razorpay_order_id);
        } else {
            // COD: group by same address and same minute of creation (heuristic)
            $groupOrdersQuery->where('address', $order->address)
                ->where('created_at', '>=', $order->created_at->subMinutes(1))
                ->where('created_at', '<=', $order->created_at->addMinutes(1));
        }

        $groupOrders = $groupOrdersQuery->get();
        $totalCartAmount = $groupOrders->sum('grand_total');

        // Identify orders in the group that have a status eligible for cashback
        $eligibleGroupOrders = $groupOrders->filter(function($o) {
            return in_array($o->order_status, ['Paid', 'Delivered']);
        });

        // The oldest (smallest ID) eligible order in the group becomes the "Claimant"
        // This ensures the cashback is only credited ONCE per checkout session,
        // and it works regardless of which item in the cart is processed first.
        $claimantOrder = $eligibleGroupOrders->sortBy('id')->first();
        
        if ($claimantOrder && $order->id == $claimantOrder->id) {
            // Now find the highest MOV threshold reached by the TOTAL cart amount
            $mov = \App\Models\Mov::where('price', '<=', $totalCartAmount)->orderBy('price', 'desc')->first();
            
            if ($mov) {
                // Check if MOV cashback already credited for ANY order in this group
                // We check all order IDs in the group for ANY existing MOV cashback transaction
                $alreadyCredited = self::whereIn('order_id', $groupOrders->pluck('id'))
                    ->where('description', 'LIKE', 'MOV cashback%')
                    ->exists();

                if (!$alreadyCredited) {
                    $cashback_amount = ($totalCartAmount * $mov->cashback_percentage) / 100;
                    $user->wallet_balance += $cashback_amount;
                    $user->save();

                    $totalCashback += $cashback_amount;

                    self::create([
                        'user_id'     => $user->id,
                        'order_id'    => $order->id,
                        'amount'      => $cashback_amount,
                        'type'        => 'credit',
                        'description' => 'MOV cashback for checkout totaling ₹' . number_format($totalCartAmount, 2),
                    ]);

                    \App\Models\Notification::create([
                        'type' => 'wallet_credit',
                        'title' => 'Cashback Credited',
                        'message' => '₹' . number_format($cashback_amount, 2) . ' cashback has been credited to your wallet for your checkout totaling ₹' . number_format($totalCartAmount, 2) . '.',
                        'related_id' => (int) $user->id,
                        'related_type' => User::class,
                        'is_read' => false,
                    ]);
                }
            }
        }

        return $totalCashback;
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

        // 1b. Revert MOV Cashback (Credit Reversal)
        $movTransaction = self::where('order_id', $orderId)
            ->where('type', 'credit')
            ->where('description', 'LIKE', 'MOV cashback%')
            ->first();
        if ($movTransaction) {
            $user->wallet_balance -= $movTransaction->amount;
            $user->save();

            self::create([
                'user_id'     => $user->id,
                'order_id'    => $order->id,
                'amount'      => $movTransaction->amount,
                'type'        => 'debit',
                'description' => 'Reversal of MOV cashback for cancelled order #' . $order->id,
            ]);

            \App\Models\Notification::create([
                'type' => 'wallet_debit',
                'title' => 'Wallet adjusted',
                'message' => 'Cashback amount ₹' . number_format($movTransaction->amount, 2) . ' has been reversed for cancelled order #' . $order->id . '.',
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
