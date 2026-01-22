<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookMeta
{
    protected $pixelId;
    protected $accessToken;

    public function __construct()
    {
        $this->pixelId = env('META_PIXEL_ID');
        $this->accessToken = env('META_ACCESS_TOKEN');
    }

    public function sendPurchaseEvent($order)
    {
        if (!$this->pixelId || !$this->accessToken) {
            return;
        }

        try {
            $userData = [
                'em' => hash('sha256', strtolower($order->user->email ?? '')),
                'client_user_agent' => request()->userAgent(),
                'client_ip_address' => request()->ip(),
            ];
            // 'fbc' => cookie('_fbc'),
            // 'fbp' => cookie('_fbp'),

            $payload = [
                'data' => [
                    [
                        'event_name' => 'Purchase',
                        'event_time' => time(),
                        'action_source' => 'website',
                        'user_data' => $userData,
                        'custom_data' => [
                            'currency' => 'BDT',
                            'value' => $order->total_amount,
                            'order_id' => $order->id,
                            'content_ids' => $order->items->pluck('product_id')->toArray(),
                            'content_type' => 'product',
                        ],
                        'event_source_url' => url()->current(),
                    ]
                ],
                // 'test_event_code' => 'TEST12345' // For testing in Events Manager
            ];

            $response = Http::post("https://graph.facebook.com/v19.0/{$this->pixelId}/events?access_token={$this->accessToken}", $payload);

            if ($response->failed()) {
                Log::error('Meta CAPI Error: ' . $response->body());
            } else {
                Log::info('Meta CAPI Success: Purchase Event Sent for Order #' . $order->id);
            }

        } catch (\Exception $e) {
            Log::error('Meta CAPI Exception: ' . $e->getMessage());
        }
    }
}
