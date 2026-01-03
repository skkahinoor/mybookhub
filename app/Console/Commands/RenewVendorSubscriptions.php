<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vendor;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RenewVendorSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor:renew-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and renew vendor Pro plan subscriptions. Downgrade expired plans to Free.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking vendor subscriptions...');

        // Find vendors with Pro plan that have expired
        $expiredVendors = Vendor::where('plan', 'pro')
            ->whereNotNull('plan_expires_at')
            ->where('plan_expires_at', '<', now())
            ->get();

        $downgradedCount = 0;

        foreach ($expiredVendors as $vendor) {
            $vendor->update([
                'plan' => 'free',
                'plan_expires_at' => null,
            ]);

            $downgradedCount++;
            
            $this->info("Downgraded vendor ID {$vendor->id} ({$vendor->name}) from Pro to Free plan.");
            
            Log::info("Vendor subscription expired and downgraded", [
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name,
                'expired_at' => $vendor->plan_expires_at,
            ]);
        }

        // Find vendors with Pro plan expiring in next 7 days (for notifications)
        $expiringSoon = Vendor::where('plan', 'pro')
            ->whereNotNull('plan_expires_at')
            ->whereBetween('plan_expires_at', [now(), now()->addDays(7)])
            ->get();

        $this->info("Found {$expiringSoon->count()} vendors with subscriptions expiring in the next 7 days.");

        $this->info("Completed! Downgraded {$downgradedCount} expired subscriptions.");

        return 0;
    }
}
