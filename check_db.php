<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Notification;

$total = Notification::count();
$sent = Notification::where('source', 'dashboard')->count();
$received = Notification::where('source', 'external')->count();

echo "=== Database Check ===\n";
echo "Total Notifications: $total\n";
echo "Sent from Dashboard: $sent\n";
echo "Received from External: $received\n";
echo "\n=== Latest 5 Notifications ===\n";

$notifications = Notification::latest()->take(5)->get();
foreach ($notifications as $n) {
    echo "ID: {$n->id} | {$n->title} | Source: {$n->source} | {$n->created_at}\n";
}
