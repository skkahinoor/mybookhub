<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\OrdersLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CancelPendingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel online prepaid orders that have been pending for more than 30 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timeLimit = Carbon::now()->subMinutes(30);

        // Find orders that are 'Pending' or 'New', and are NOT COD or PICKUP, and are older than 30 mins
        $pendingOrders = Order::whereIn('order_status', ['Pending', 'New'])
            ->whereNotIn('payment_gateway', ['COD', 'PICKUP'])
            ->where('created_at', '<', $timeLimit)
            ->get();

        if ($pendingOrders->isEmpty()) {
            $this->info('No pending online orders found older than 30 minutes.');
            return;
        }

        $count = 0;
        foreach ($pendingOrders as $order) {
            // Update Order Status
            $order->update(['order_status' => 'Cancelled']);

            // Update Item Statuses
            OrdersProduct::where('order_id', $order->id)->update([
                'item_status' => 'Cancelled'
            ]);

            // Refund wallet if used
            if ($order->wallet_amount > 0) {
                WalletTransaction::revertWallet($order->id);
            }

            // Create Log
            $log = new OrdersLog;
            $log->order_id = $order->id;
            $log->order_status = 'Cancelled';
            $log->save();

            // Notify User
            if ($order->user_id) {
                Notification::create([
                    'type' => 'order_canceled_system',
                    'title' => 'Order Cancelled (Payment Timeout)',
                    'message' => 'Your order #' . $order->id . ' has been cancelled automatically because the payment was not completed within 30 minutes. If any wallet amount was used, it has been refunded.',
                    'related_id' => (int) $order->user_id,
                    'related_type' => User::class,
                    'is_read' => false,
                ]);
            }

            $count++;
        }

        $this->info("Successfully cancelled {$count} pending online order(s).");
        Log::info("CancelPendingOrders: Successfully cancelled {$count} pending online order(s).");
    }
}
