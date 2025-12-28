<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$notifications = App\Models\Notification::all();

echo "Total Notifications: " . $notifications->count() . "\n\n";

foreach ($notifications as $notification) {
    echo "ID: " . $notification->id . "\n";
    echo "Title: " . $notification->title . "\n";
    echo "Message: " . $notification->message . "\n";
    echo "Data: " . json_encode($notification->data, JSON_PRETTY_PRINT) . "\n";
    echo "Created: " . $notification->created_at . "\n";
    echo str_repeat("-", 50) . "\n\n";
}
