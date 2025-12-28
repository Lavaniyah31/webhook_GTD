<?php

use Illuminate\Support\Facades\Route;
use App\Models\Notification;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Main webhook dashboard - send and receive webhooks
Route::get('/dashboard', function () {
    return view('webhook-dashboard');
});

// API endpoint to get all notifications with counts
Route::get('/notifications', function () {
    $notifications = Notification::latest()->get();
    $totalSent = Notification::where('source', 'dashboard')->count();
    $totalReceived = Notification::where('source', 'external')->count();
    
    return response()->json([
        'total' => $notifications->count(),
        'total_sent' => $totalSent,
        'total_received' => $totalReceived,
        'notifications' => $notifications
    ], 200, [], JSON_PRETTY_PRINT);
});
