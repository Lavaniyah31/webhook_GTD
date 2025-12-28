
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationWebhookController;

Route::get('/test', function() {
    return response()->json(['status' => 'API routes working']);
});

// Webhook endpoints
Route::post('/webhook/notification', [NotificationWebhookController::class, 'receive']);
Route::post('/webhook/send', [NotificationWebhookController::class, 'send']);
