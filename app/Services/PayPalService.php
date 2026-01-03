<?php

namespace App\Services;

use App\Models\Setting;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected ?PayPalHttpClient $client = null;

    public function __construct()
    {
        $this->initializeClient();
    }

    protected function initializeClient(): void
    {
        $clientId = $this->getClientId();
        $clientSecret = $this->getClientSecret();

        if (!$clientId || !$clientSecret) {
            return;
        }

        $mode = Setting::get('paypal_mode', 'sandbox');

        if ($mode === 'live') {
            $environment = new ProductionEnvironment($clientId, $clientSecret);
        } else {
            $environment = new SandboxEnvironment($clientId, $clientSecret);
        }

        $this->client = new PayPalHttpClient($environment);
    }

    public function isConfigured(): bool
    {
        return $this->client !== null && $this->isEnabled();
    }

    public function isEnabled(): bool
    {
        return (bool) Setting::get('paypal_enabled', false);
    }

    public function getClientId(): ?string
    {
        return Setting::get('paypal_client_id') ?: config('services.paypal.client_id');
    }

    public function getClientSecret(): ?string
    {
        return Setting::get('paypal_secret') ?: config('services.paypal.secret');
    }

    public function getCurrency(): string
    {
        return strtoupper(Setting::get('paypal_currency', 'usd'));
    }

    /**
     * Create PayPal order for checkout
     */
    public function createOrder(array $data): ?array
    {
        if (!$this->client) {
            Log::error('PayPal client not initialized');
            return null;
        }

        try {
            $request = new OrdersCreateRequest();
            $request->prefer('return=representation');
            $request->body = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $data['order_number'],
                    'description' => 'Order #' . $data['order_number'],
                    'amount' => [
                        'currency_code' => $this->getCurrency(),
                        'value' => number_format($data['total'], 2, '.', ''),
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => $this->getCurrency(),
                                'value' => number_format($data['subtotal'], 2, '.', ''),
                            ],
                            'tax_total' => [
                                'currency_code' => $this->getCurrency(),
                                'value' => number_format($data['tax_amount'] ?? 0, 2, '.', ''),
                            ],
                        ],
                    ],
                    'items' => $this->formatItems($data['items'] ?? []),
                ]],
                'application_context' => [
                    'brand_name' => config('app.name', 'Codexse'),
                    'landing_page' => 'NO_PREFERENCE',
                    'user_action' => 'PAY_NOW',
                    'return_url' => $data['return_url'],
                    'cancel_url' => $data['cancel_url'],
                ],
            ];

            $response = $this->client->execute($request);

            if ($response->statusCode === 201) {
                $result = $response->result;

                // Find approval link
                $approvalUrl = null;
                foreach ($result->links as $link) {
                    if ($link->rel === 'approve') {
                        $approvalUrl = $link->href;
                        break;
                    }
                }

                return [
                    'id' => $result->id,
                    'status' => $result->status,
                    'approval_url' => $approvalUrl,
                ];
            }

            Log::error('PayPal order creation failed', ['status' => $response->statusCode]);
            return null;

        } catch (\Exception $e) {
            Log::error('PayPal order creation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Capture PayPal order after approval
     */
    public function captureOrder(string $orderId): ?array
    {
        if (!$this->client) {
            Log::error('PayPal client not initialized');
            return null;
        }

        try {
            $request = new OrdersCaptureRequest($orderId);
            $request->prefer('return=representation');

            $response = $this->client->execute($request);

            if ($response->statusCode === 201) {
                $result = $response->result;

                return [
                    'id' => $result->id,
                    'status' => $result->status,
                    'payer_email' => $result->payer->email_address ?? null,
                    'capture_id' => $result->purchase_units[0]->payments->captures[0]->id ?? null,
                ];
            }

            Log::error('PayPal capture failed', ['status' => $response->statusCode]);
            return null;

        } catch (\Exception $e) {
            Log::error('PayPal capture error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get PayPal order details
     */
    public function getOrder(string $orderId): ?array
    {
        if (!$this->client) {
            return null;
        }

        try {
            $request = new OrdersGetRequest($orderId);
            $response = $this->client->execute($request);

            if ($response->statusCode === 200) {
                $result = $response->result;
                return [
                    'id' => $result->id,
                    'status' => $result->status,
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('PayPal get order error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format items for PayPal
     */
    protected function formatItems(array $items): array
    {
        $formattedItems = [];

        foreach ($items as $item) {
            $formattedItems[] = [
                'name' => substr($item['name'], 0, 127), // PayPal has 127 char limit
                'quantity' => '1',
                'category' => 'DIGITAL_GOODS',
                'unit_amount' => [
                    'currency_code' => $this->getCurrency(),
                    'value' => number_format($item['price'], 2, '.', ''),
                ],
            ];
        }

        return $formattedItems;
    }
}
