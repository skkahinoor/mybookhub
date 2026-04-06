<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

// Load Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$phone = '1234567890';

echo "Testing Resend OTP for Phone: $phone\n";

// 1. Setup cache for registration session
Cache::put('student_reg_name_' . $phone, 'Test User', now()->addMinutes(10));
echo "Setup registration session in cache.\n";

// 2. Clear existing OTP
DB::table('otps')->where('phone', $phone)->delete();
echo "Cleared existing OTP from database.\n";

// 3. Call resendOtp
$request = new \Illuminate\Http\Request(['phone' => $phone]);
$response = app(\App\Http\Controllers\Api\User\ProfileController::class)->resendOtp($request);

echo "Resend OTP status: " . $response->getStatusCode() . "\n";
$content = json_decode($response->getContent());
echo "Message: " . ($content->message ?? 'No message') . "\n";

// 4. Verify DB
$otpRecord = DB::table('otps')->where('phone', $phone)->first();
if ($otpRecord) {
    echo "Found OTP in DB: {$otpRecord->otp} (Created at: {$otpRecord->created_at})\n";
} else {
    echo "OTP not found in DB!\n";
}

// 5. Test expired session
Cache::forget('student_reg_name_' . $phone);
$response2 = app(\App\Http\Controllers\Api\User\ProfileController::class)->resendOtp($request);
echo "Resend OTP (Expired Session) status: " . $response2->getStatusCode() . "\n";
echo "Message: " . json_decode($response2->getContent())->message . "\n";

// Cleanup
DB::table('otps')->where('phone', $phone)->delete();
echo "Cleaned up.\n";
