<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Webhook Endpoints Configuration
    |--------------------------------------------------------------------------
    |
    | Configure external webhook URLs where your app will SEND webhooks.
    | When you receive or send a webhook, it will be forwarded to all URLs below.
    |
    | TO ADD A WEBHOOK ENDPOINT:
    | 1. Go to https://webhook.site (or any webhook receiver)
    | 2. Copy the unique URL provided
    | 3. Add it to the 'external_urls' array below
    | 4. Save this file
    |
    | Example:
    | 'external_urls' => [
    |     'https://webhook.site/your-unique-id',
    |     'https://your-app.com/api/webhook',
    | ],
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
