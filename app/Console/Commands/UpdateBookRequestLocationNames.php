<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BookRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateBookRequestLocationNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'book-requests:update-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update missing user_location_name for book requests using Google Maps API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = config('services.google.maps_key');
        if (!$apiKey) {
            $this->error('Google Maps API key not found in config/services.php');
            return;
        }

        $requests = BookRequest::whereNotNull('user_location')
            ->where(function($q) {
                $q->whereNull('user_location_name')->orWhere('user_location_name', '');
            })
            ->get();

        $this->info('Found ' . $requests->count() . ' requests to update.');

        foreach ($requests as $request) {
            $this->info('Updating ID: ' . $request->id . ' (Coords: ' . $request->user_location . ')');
            
            try {
                $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'latlng' => $request->user_location,
                    'key' => $apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['results'][0]['formatted_address'])) {
                        $address = $data['results'][0]['formatted_address'];
                        $request->update(['user_location_name' => $address]);
                        $this->line('  - Success: ' . $address);
                    } else {
                        $this->warn('  - No address found for these coordinates.');
                    }
                } else {
                    $this->error('  - API Request failed: ' . $response->status());
                }
            } catch (\Exception $e) {
                $this->error('  - Error: ' . $e->getMessage());
            }

            // Small delay to be polite to the API
            usleep(200000); 
        }

        $this->info('Update completed.');
    }
}
