<?php

use Illuminate\Support\Facades\Route;
use App\Models\Notification;

Route::get('/', function () {
    return redirect('/dashboard');
});

/*
|--------------------------------------------------------------------------
| COMPLETE WEBHOOK DASHBOARD
|--------------------------------------------------------------------------
| Test both sending AND receiving webhooks in real-time
*/
Route::get('/dashboard', function () {
    return view('webhook-dashboard');
});

/*
|--------------------------------------------------------------------------
| REAL-TIME NOTIFICATIONS PAGE
|--------------------------------------------------------------------------
| Live dashboard that auto-updates when new webhooks arrive
*/
Route::get('/notifications-live', function () {
    return view('notifications-realtime');
});

/*
|--------------------------------------------------------------------------
| VIEW ALL RECEIVED WEBHOOKS
|--------------------------------------------------------------------------
| Shows all webhooks that were RECEIVED by your application
*/
Route::get('/notifications', function () {
    $notifications = Notification::latest()->get();
    
    return response()->json([
        'total' => $notifications->count(),
        'notifications' => $notifications
    ], 200, [], JSON_PRETTY_PRINT);
});

/*
|--------------------------------------------------------------------------
| WEBHOOK SYSTEM TEST & INFO PAGE
|--------------------------------------------------------------------------
| Visit this URL in browser to see how to test both types of webhooks
*/
Route::get('/test-webhooks', function () {
    $externalUrls = config('webhooks.external_urls', []);
    $totalReceived = Notification::count();
    
    return response()->json([
        '📥 RECEIVING WEBHOOKS' => [
            'status' => '✅ Ready',
            'endpoint' => url('/api/webhook/notification'),
            'method' => 'POST',
            'total_received_so_far' => $totalReceived,
            'how_to_test' => [
                'step_1' => 'Open Postman',
                'step_2' => 'Method: POST',
                'step_3' => 'URL: ' . url('/api/webhook/notification'),
                'step_4' => 'Body (JSON): {"title":"Test","message":"Hello"}',
                'step_5' => 'Click Send',
                'step_6' => 'Check ' . url('/notifications') . ' to see stored webhook'
            ]
        ],
        '📤 SENDING WEBHOOKS' => [
            'status' => empty($externalUrls) ? '⚠️ Not configured yet' : '✅ Configured',
            'configured_destinations' => empty($externalUrls) ? 'None - Add URLs to config/webhooks.php' : $externalUrls,
            'how_to_configure' => [
                'step_1' => 'Visit https://webhook.site in your browser',
                'step_2' => 'Copy the unique URL shown (e.g., https://webhook.site/abc-123)',
                'step_3' => 'Open config/webhooks.php in VS Code',
                'step_4' => 'Add your URL to the external_urls array',
                'step_5' => 'Save the file'
            ],
            'how_it_works' => 'After configuration, when your app RECEIVES a webhook, it will automatically SEND the same data to all configured URLs'
        ],
        '🧪 QUICK TEST STEPS' => [
            '1_RECEIVE' => 'Send POST request to ' . url('/api/webhook/notification'),
            '2_VERIFY_RECEIVED' => 'Visit ' . url('/notifications') . ' to see it was stored',
            '3_VERIFY_SENT' => 'If external URLs configured, check those websites to see forwarded data'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
});
