<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('name', 'Pragya Panda')->first();
if($user) {
    echo "User ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
    echo "role_id column: " . $user->role_id . "\n";
    echo "Permissions: " . implode(', ', $user->getAllPermissions()->pluck('name')->toArray()) . "\n";
    echo "Can view_orders: " . ($user->can('view_orders') ? 'YES' : 'NO') . "\n";
    echo "Guard: " . config('auth.defaults.guard') . "\n";
} else {
    echo "User Pragya Panda not found\n";
    // List some users to be sure
    $users = App\Models\User::take(5)->pluck('name');
    echo "Available users: " . implode(', ', $users->toArray()) . "\n";
}
