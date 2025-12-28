
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationWebhookController;

Route::get('/test', function() {
    return response()->json(['status' => 'API routes working']);
});

Route::post('/webhook/notification', [NotificationWebhookController::class, 'receive']);

