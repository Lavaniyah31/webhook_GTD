<?php

return [
    /*
    |--------------------------------------------------------------------------
    | External Webhook URLs - WHERE TO SEND WEBHOOKS
    |--------------------------------------------------------------------------
    |
    | IMPORTANT: Add URLs where you want to SEND/FORWARD webhooks
    | 
    | HOW IT WORKS:
    | When your app RECEIVES a webhook → It automatically SENDS that 
    | same data to all URLs listed below.
    |
    | TO TEST SENDING:
    | 1. Go to https://webhook.site
    | 2. Copy your unique URL (e.g., https://webhook.site/abc-123)
    | 3. Add it below
    | 4. Send a webhook to your Laravel app
    | 5. Check webhook.site - you'll see the forwarded data!
    |
    */
    'external_urls' => [
        'https://webhook.site/2417ee09-4978-4ee5-9a00-4028b8651ba7',
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Timeout
    |--------------------------------------------------------------------------
    |
    | How many seconds to wait before giving up when sending webhooks.
    |
    */
    'timeout' => 10,
];
