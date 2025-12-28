<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class NotificationWebhookController extends Controller
{
    public function receive(Request $request)
    {
        try {
            Log::info('Webhook received', $request->all());
            
            // Store the notification in database
            $notification = Notification::create([
                'title'   => $request->input('title', 'New Notification'),
                'message' => $request->input('message'),
                'data'    => $request->all(),
                'source'  => $request->input('source', 'external')
            ]);

            // Forward webhook to external services
            $this->forwardToExternalServices($request->all());

            return response()->json([
                'status' => 'saved',
                'message' => 'Notification stored and forwarded successfully',
                'id' => $notification->id
            ], 200);
        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forward webhook data to external services
     */
    private function forwardToExternalServices(array $data)
    {
        $externalUrls = config('webhooks.external_urls', []);
        $timeout = config('webhooks.timeout', 10);

        foreach ($externalUrls as $url) {
            try {
                /** @var Response $response */
                $response = Http::timeout($timeout)
                    ->post($url, $data);

                $statusCode = $response->status();
                
                if ($statusCode >= 200 && $statusCode < 300) {
                    Log::info("Webhook forwarded successfully to: {$url}");
                } else {
                    Log::warning("Webhook forward failed to: {$url}, Status: " . $statusCode);
                }
            } catch (\Exception $e) {
                Log::error("Failed to forward webhook to: {$url}, Error: {$e->getMessage()}");
            }
        }
    }
}
