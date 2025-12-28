<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class NotificationWebhookController extends Controller
{
    /**
     * RECEIVE webhook from external services
     * Endpoint: POST /api/webhook/notification
     */
    public function receive(Request $request)
    {
        try {
            Log::info(' Webhook RECEIVED from external source', $request->all());
            
            // Store the notification in database
            $notification = Notification::create([
                'title'   => $request->input('title', 'New Notification'),
                'message' => $request->input('message'),
                'data'    => $request->all(),
                'source'  => $request->input('source', 'external')
            ]);

            // Forward to configured webhook endpoints
            $this->sendToWebhooks($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook received and forwarded',
                'id' => $notification->id
            ], 200);
        } catch (\Exception $e) {
            Log::error(' Webhook receive error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * SEND webhook to external services
     * Endpoint: POST /api/webhook/send
     */
    public function send(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'message' => 'nullable|string',
            ]);

            Log::info(' Webhook SEND triggered from app', $request->all());
            
            // Store the notification in database
            $notification = Notification::create([
                'title'   => $request->input('title'),
                'message' => $request->input('message'),
                'data'    => $request->all(),
                'source'  => 'dashboard'
            ]);

            // Send to configured webhook endpoints
            $results = $this->sendToWebhooks($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook sent to configured endpoints',
                'id' => $notification->id,
                'forwarded_to' => count($results),
                'results' => $results
            ], 200);
        } catch (\Exception $e) {
            Log::error(' Webhook send error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send webhook data to configured external webhook endpoints
     */
    private function sendToWebhooks(array $data)
    {
        $webhookUrls = config('webhooks.external_urls', []);
        $timeout = config('webhooks.timeout', 10);
        $results = [];

        foreach ($webhookUrls as $url) {
            try {
                /** @var Response $response */
                $response = Http::timeout($timeout)
                    ->withHeaders([
                        'User-Agent' => 'Laravel-Webhook-Sender/1.0',
                        'X-Webhook-Source' => 'Laravel-App'
                    ])
                    ->post($url, $data);

                $statusCode = $response->status();
                
                if ($statusCode >= 200 && $statusCode < 300) {
                    Log::info(" Webhook sent successfully to: {$url}");
                    $results[] = [
                        'url' => $url,
                        'status' => 'success',
                        'code' => $statusCode
                    ];
                } else {
                    Log::warning("⚠️ Webhook failed to: {$url}, Status: {$statusCode}");
                    $results[] = [
                        'url' => $url,
                        'status' => 'failed',
                        'code' => $statusCode
                    ];
                }
            } catch (\Exception $e) {
                Log::error(" Failed to send webhook to: {$url}, Error: {$e->getMessage()}");
                $results[] = [
                    'url' => $url,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }

        return $results;
    }
}
